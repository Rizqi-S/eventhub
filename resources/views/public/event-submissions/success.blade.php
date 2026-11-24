@extends('public.layout')

@section('title', 'Pengajuan Terkirim')

@section('content')

    <section class="public-section" style="max-width:760px;margin:70px auto;text-align:center">
        <p class="eyebrow">PENGAJUAN DITERIMA</p>

        <h1>Terima kasih sudah mengirim event!</h1>

        <p class="muted">
            Pengajuanmu sudah masuk ke antrean moderasi EventHub.
            Event belum ditampilkan ke publik sampai admin menyetujuinya.
        </p>

        <a class="btn primary" href="{{ route('home') }}">
            Kembali ke beranda
        </a>
    </section>

@endsection