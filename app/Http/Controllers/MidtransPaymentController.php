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
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function pay(string $token)
    {
        $pendaftaran = Pendaftaran::with(['peserta', 'event'])
            ->where('public_token', $token)
            ->firstOrFail();

        if ($pendaftaran->status_pembayaran === 'lunas') {
            return redirect()->route(
                'public.registration.confirmation',
                $token
            );
        }

        if ($pendaftaran->status_pembayaran !== 'belum_bayar') {
            return back()->with('error', 'Pendaftaran tidak dapat dibayar.');
        }

        if (! $pendaftaran->expires_at || $pendaftaran->expires_at->isPast()) {
            $pendaftaran->update(['status_pembayaran' => 'kedaluwarsa']);

            return redirect()->route(
                'public.registration.confirmation',
                $token
            )->with('error', 'Batas waktu pembayaran sudah habis.');
        }

        if ($pendaftaran->snap_token) {
            $baseUrl = config('services.midtrans.is_production')
                ? 'https://app.midtrans.com'
                : 'https://app.sandbox.midtrans.com';

            return redirect()->away(
                $baseUrl . '/snap/v4/redirection/' . $pendaftaran->snap_token
            );
        }

        $this->configureMidtrans();

        $orderId = 'EVH-' . $pendaftaran->id . '-' .
            Str::upper(Str::random(10));

        $amount = (int) round((float) $pendaftaran->event->biaya);

        if ($amount < 1) {
            return back()->with('error', 'Nominal pembayaran tidak valid.');
        }

        try {
            $transaction = Snap::createTransaction([
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
                'callbacks' => [
                    'finish' => route(
                        'public.registration.confirmation',
                        $token
                    ),
                ],
            ]);

            $pendaftaran->update([
                'midtrans_order_id' => $orderId,
                'snap_token' => $transaction->token,
            ]);

            return redirect()->away($transaction->redirect_url);

        } catch (\Throwable $e) {
            Log::error('Midtrans create transaction failed', [
                'pendaftaran_id' => $pendaftaran->id,
                'message' => $e->getMessage(),
            ]);

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

        $signatureKey = config('services.midtrans.signature_key');

        if (! $signatureKey || ! $orderId || ! $signature) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $expectedSignature = hash(
            'sha512',
            $orderId . $statusCode . $grossAmount . $signatureKey
        );

        if (! hash_equals($expectedSignature, $signature)) {
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

            // Ambil status langsung dari Midtrans sebagai verifikasi tambahan.
            $transaction = Transaction::status($orderId);

            if (
                (string) $transaction->order_id !== $orderId ||
                (string) $transaction->gross_amount !== (string) $grossAmount
            ) {
                return response()->json(['message' => 'Transaction mismatch'], 400);
            }

            DB::transaction(function () use ($transaction, $pendaftaran) {
                $record = Pendaftaran::whereKey($pendaftaran->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $status = $transaction->transaction_status;
                $fraudStatus = $transaction->fraud_status ?? null;

                if (
                    $status === 'settlement' ||
                    ($status === 'capture' && $fraudStatus === 'accept')
                ) {
                    $record->update([
                        'status_pembayaran' => 'lunas',
                        'midtrans_transaction_id' => $transaction->transaction_id ?? null,
                        'payment_type' => $transaction->payment_type ?? null,
                        'paid_at' => $record->paid_at ?? now(),
                    ]);
                } elseif (
                    in_array($status, ['expire', 'cancel', 'deny'], true) &&
                    $record->status_pembayaran !== 'lunas'
                ) {
                    $record->update([
                        'status_pembayaran' => 'kedaluwarsa',
                        'midtrans_transaction_id' => $transaction->transaction_id ?? null,
                        'payment_type' => $transaction->payment_type ?? null,
                    ]);
                } elseif ($status === 'pending') {
                    $record->update([
                        'midtrans_transaction_id' => $transaction->transaction_id ?? null,
                        'payment_type' => $transaction->payment_type ?? null,
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