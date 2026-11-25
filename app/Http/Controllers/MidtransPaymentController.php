<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransPaymentController extends Controller
{
    private function configureMidtrans(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = filter_var(config('services.midtrans.is_production'), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function pay(Request $request, string $token)
    {
        $pendaftaran = Pendaftaran::with(['peserta', 'event'])
            ->where('public_token', $token)
            ->firstOrFail();

        if ($pendaftaran->status_pembayaran === 'lunas') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'already_paid',
                    'message' => 'Pendaftaran ini sudah berstatus lunas.',
                    'redirect_url' => route('public.registration.confirmation', $token),
                ]);
            }

            return redirect()->route(
                'public.registration.confirmation',
                $token
            );
        }

        if ($pendaftaran->status_pembayaran !== 'belum_bayar') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'invalid_status',
                    'message' => 'Pendaftaran tidak dapat dibayar.',
                ], 422);
            }

            return back()->with('error', 'Pendaftaran tidak dapat dibayar.');
        }

        if (! $pendaftaran->expires_at || $pendaftaran->expires_at->isPast()) {
            $pendaftaran->update(['status_pembayaran' => 'kedaluwarsa']);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'expired',
                    'message' => 'Batas waktu pembayaran sudah habis.',
                    'redirect_url' => route('public.registration.confirmation', $token),
                ], 422);
            }

            return redirect()->route(
                'public.registration.confirmation',
                $token
            )->with('error', 'Batas waktu pembayaran sudah habis.');
        }

        // Popup Mode: Jika snap_token sudah ada sebelumnya, kirim kembali token tersebut
        if ($pendaftaran->snap_token) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'snap_token' => $pendaftaran->snap_token,
                    'order_id' => $pendaftaran->midtrans_order_id,
                ]);
            }

            return redirect()->route('public.registration.confirmation', $token);
        }

        $this->configureMidtrans();

        $orderId = 'EVH-' . $pendaftaran->id . '-' .
            Str::upper(Str::random(10));

        $amount = (int) round((float) $pendaftaran->event->biaya);

        if ($amount < 1) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Nominal pembayaran tidak valid.'], 422);
            }

            return back()->with('error', 'Nominal pembayaran tidak valid.');
        }

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $amount,
                ],
                'customer_details' => [
                    'first_name' => $pendaftaran->peserta->nama,
                    'email' => $pendaftaran->peserta->email,
                    'phone' => $pendaftaran->peserta->no_hp,
                ],
                'item_details' => [[
                    'id' => 'EVENT-' . $pendaftaran->event_id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => Str::limit(
                        $pendaftaran->event->nama_event,
                        50
                    ),
                ]],
            ];

            $transaction = Snap::createTransaction($params);
            $snapToken = $transaction->token ?? Snap::getSnapToken($params);

            $pendaftaran->update([
                'midtrans_order_id' => $orderId,
                'snap_token' => $snapToken,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'snap_token' => $snapToken,
                    'order_id' => $orderId,
                ]);
            }

            return redirect()->route('public.registration.confirmation', $token);

        } catch (\Throwable $e) {
            Log::error('Midtrans create transaction failed', [
                'pendaftaran_id' => $pendaftaran->id,
                'message' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Gagal membuat pembayaran Midtrans. Silakan coba lagi.',
                ], 500);
            }

            return back()->with(
                'error',
                'Gagal membuat pembayaran. Silakan coba lagi.'
            );
        }
    }

    public function notification(Request $request)
    {
        $data = $request->all();

        $orderId = $data['order_id'] ?? '';
        $statusCode = $data['status_code'] ?? '';
        $grossAmount = $data['gross_amount'] ?? '';
        $signature = $data['signature_key'] ?? '';

        $serverKey = config('services.midtrans.server_key');
        $signatureKey = config('services.midtrans.signature_key') ?: $serverKey;

        if ((! $signatureKey && ! $serverKey) || ! $orderId || ! $signature) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        // Verifikasi signature key sesuai rumus Midtrans: SHA512(order_id + status_code + gross_amount + ServerKey)
        $isValidSignature = false;
        if ($signatureKey) {
            $expectedSignature = hash(
                'sha512',
                $orderId . $statusCode . $grossAmount . $signatureKey
            );
            if (hash_equals($expectedSignature, $signature)) {
                $isValidSignature = true;
            }
        }

        if (! $isValidSignature && $serverKey && $serverKey !== $signatureKey) {
            $expectedSignature = hash(
                'sha512',
                $orderId . $statusCode . $grossAmount . $serverKey
            );
            if (hash_equals($expectedSignature, $signature)) {
                $isValidSignature = true;
            }
        }

        if (! $isValidSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pendaftaran = Pendaftaran::where(
            'midtrans_order_id',
            $orderId
        )->first();

        if (! $pendaftaran) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        try {
            $this->configureMidtrans();

            $transactionStatus = null;
            $fraudStatus = null;
            $transactionId = null;
            $paymentType = null;

            try {
                // Ambil status langsung dari Midtrans sebagai verifikasi ganda.
                $transaction = Transaction::status($orderId);

                if (
                    (string) $transaction->order_id !== $orderId ||
                    (string) $transaction->gross_amount !== (string) $grossAmount
                ) {
                    return response()->json(['message' => 'Transaction mismatch'], 400);
                }

                $transactionStatus = $transaction->transaction_status ?? null;
                $fraudStatus = $transaction->fraud_status ?? null;
                $transactionId = $transaction->transaction_id ?? null;
                $paymentType = $transaction->payment_type ?? null;
            } catch (\Throwable $statusException) {
                Log::warning('Midtrans Transaction::status call failed, using verified notification payload', [
                    'order_id' => $orderId,
                    'error' => $statusException->getMessage(),
                ]);

                // Signature telah terverifikasi via SHA-512 dengan ServerKey rahasia,
                // sehingga data payload aman digunakan saat status call terkendala.
                $transactionStatus = $data['transaction_status'] ?? null;
                $fraudStatus = $data['fraud_status'] ?? null;
                $transactionId = $data['transaction_id'] ?? null;
                $paymentType = $data['payment_type'] ?? null;
            }

            if (! $transactionStatus) {
                return response()->json(['message' => 'Missing transaction status'], 400);
            }

            DB::transaction(function () use ($pendaftaran, $transactionStatus, $fraudStatus, $transactionId, $paymentType) {
                $record = Pendaftaran::whereKey($pendaftaran->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $transactionStatus === 'settlement' ||
                    ($transactionStatus === 'capture' && $fraudStatus === 'accept')
                ) {
                    $record->update([
                        'status_pembayaran' => 'lunas',
                        'midtrans_transaction_id' => $transactionId,
                        'payment_type' => $paymentType,
                        'paid_at' => $record->paid_at ?? now(),
                    ]);
                } elseif (
                    in_array($transactionStatus, ['expire', 'cancel', 'deny'], true) &&
                    $record->status_pembayaran !== 'lunas'
                ) {
                    $record->update([
                        'status_pembayaran' => 'kedaluwarsa',
                        'midtrans_transaction_id' => $transactionId,
                        'payment_type' => $paymentType,
                    ]);
                } elseif ($transactionStatus === 'pending') {
                    $record->update([
                        'midtrans_transaction_id' => $transactionId,
                        'payment_type' => $paymentType,
                    ]);
                }
            });

            return response()->json(['message' => 'Notification processed']);

        } catch (\Throwable $e) {
            Log::error('Midtrans notification failed', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Processing failed'], 500);
        }
    }
}
