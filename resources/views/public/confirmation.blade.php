@extends('public.layout')
@section('title','Konfirmasi Pendaftaran')
@section('content')
<section class="public-section confirmation">
    <div class="success-mark">✓</div>
    <p class="eyebrow">REGISTRATION RECEIVED</p>
    <h1>Pendaftaran berhasil dikirim</h1>
    <p class="muted">Simpan halaman ini sebagai bukti bahwa pendaftaranmu sudah diterima sistem.</p>
    <div class="confirmation-card">
        <div>
            <small>KODE PRESENSI</small>
            <strong>{{ $pendaftaran->attendance_code }}</strong>
        </div>
        <div>
            <small>NAMA</small>
            <strong>{{ $pendaftaran->peserta->nama }}</strong>
        </div>
        <div>
            <small>EVENT</small>
            <strong>{{ $pendaftaran->event->nama_event }}</strong>
        </div>
        <div>
            <small>STATUS PEMBAYARAN</small>
            <strong>{{ ucfirst(str_replace('_', ' ', $pendaftaran->status_pembayaran)) }}</strong>
            @if($pendaftaran->status_pembayaran === 'belum_bayar' && $pendaftaran->expires_at)
                <small>Batas pembayaran: {{ $pendaftaran->expires_at->format('d M Y H:i') }}</small>
            @endif
        </div>
    </div>
    @if(in_array($pendaftaran->status_pembayaran, ['lunas', 'gratis'], true))
        <a class="btn primary" href="{{ route('public.attendance.form') }}">Lakukan presensi</a>
    @elseif($pendaftaran->status_pembayaran === 'belum_bayar')
        <p class="muted">
            Selesaikan pembayaran sebelum batas waktu agar slot event tetap tersedia.
        </p>

        <form id="payment-form" method="POST" action="{{ route('public.payment.pay', $pendaftaran->public_token) }}">
            @csrf
            <button type="button" id="pay-button" class="btn primary">
                Bayar Sekarang
            </button>
        </form>
    @endif
    <a class="btn outline" href="{{ route('public.events.index') }}">Jelajahi event lainnya</a>
</section>
@endsection

@php
    $isProduction = filter_var(config('services.midtrans.is_production'), FILTER_VALIDATE_BOOLEAN);
    $snapJsUrl = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
    $clientKey = config('services.midtrans.client_key');
@endphp

@push('scripts')
@if($pendaftaran->status_pembayaran === 'belum_bayar')
<script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const payButton = document.getElementById('pay-button');
    if (!payButton) return;

    payButton.addEventListener('click', async function (e) {
        e.preventDefault();

        if (typeof window.snap === 'undefined') {
            alert('Midtrans Snap SDK sedang tidak tersedia. Periksa koneksi internet Anda atau coba muat ulang halaman.');
            return;
        }

        payButton.disabled = true;
        const originalText = payButton.textContent;
        payButton.textContent = 'Memuat pembayaran...';

        function openSnap(token) {
            window.snap.pay(token, {
                onSuccess: function (result) {
                    payButton.textContent = 'Memverifikasi pembayaran...';
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                },
                onPending: function (result) {
                    payButton.textContent = 'Menunggu pembayaran...';
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                },
                onError: function (result) {
                    alert('Pembayaran gagal atau terjadi kesalahan. Silakan coba lagi.');
                    payButton.disabled = false;
                    payButton.textContent = originalText;
                },
                onClose: function () {
                    // Menutup popup Midtrans Snap tidak mengubah status transaksi menjadi berhasil atau gagal.
                    payButton.disabled = false;
                    payButton.textContent = originalText;
                }
            });
        }

        try {
            const response = await fetch("{{ route('public.payment.pay', $pendaftaran->public_token) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (!response.ok || !data.snap_token) {
                alert(data.message || 'Gagal memproses pembayaran. Silakan coba lagi.');
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    payButton.disabled = false;
                    payButton.textContent = originalText;
                }
                return;
            }

            openSnap(data.snap_token);
        } catch (error) {
            console.error('Payment error:', error);
            alert('Terjadi kesalahan koneksi saat memproses pembayaran.');
            payButton.disabled = false;
            payButton.textContent = originalText;
        }
    });
});
</script>
@endif
@endpush
