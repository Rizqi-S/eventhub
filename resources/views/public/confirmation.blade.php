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

        <form method="POST" action="{{ route('public.payment.pay', $pendaftaran->public_token) }}">
            @csrf
            <button type="submit" class="btn primary">
                Bayar Sekarang
            </button>
        </form>
    @endif
    <a class="btn outline" href="{{ route('public.events.index') }}">Jelajahi event lainnya</a>
</section>@endsection
