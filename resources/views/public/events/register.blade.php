@extends('public.layout')
@section('title','Pendaftaran '.$event->nama_event)
@push('styles')
<style>
.form-section .stack-form {
    margin-top: 20px;
    padding: 24px;
    background: var(--surface, #20242b);
    border: 1px solid var(--border, #39414c);
    border-radius: 16px;
}
.form-section .stack-form input:not([type="checkbox"]),
.form-section .stack-form select,
.form-section .stack-form textarea {
    width: 100%;
    min-height: 44px;
    padding: 12px 14px;
    background: #252525;
    color: #eee;
    border: 1px solid #414141;
    border-radius: 9px;
}
.form-section .stack-form input:not([type="checkbox"]):focus,
.form-section .stack-form select:focus,
.form-section .stack-form textarea:focus {
    outline: none;
    border-color: var(--teal);
    box-shadow: 0 0 0 3px rgba(85, 214, 194, .1);
}
</style>
@endpush
@section('content')
<section class="public-section form-section">
    <a class="text-link" href="{{ route('public.events.show',$event) }}">← Kembali ke detail event</a>
    <p class="eyebrow">REGISTRATION FORM</p>
    <h1>Daftar event</h1>
    <p class="muted">{{ $event->nama_event }} · {{ $event->tanggal_mulai->format('d M Y') }}</p>
    @if($errors->any())
        <div class="alert error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    <form method="POST" action="{{ route('public.events.store',$event) }}" class="stack-form">
        @csrf
        <label>Nama lengkap
            <input name="nama" value="{{ old('nama') }}" required maxlength="255">
        </label>
        <label>Email aktif
            <input type="email" name="email" value="{{ old('email') }}" required maxlength="255">
        </label>
        <label>Nomor HP
            <input type="tel" name="no_hp" value="{{ old('no_hp') }}" required minlength="8" maxlength="16" pattern="\+?[0-9]{8,15}" inputmode="tel" placeholder="081234567890">
            <small class="muted">8–15 digit, boleh diawali +62.</small>
        </label>
        <label>Instansi
            <input name="instansi" value="{{ old('instansi') }}" required maxlength="255">
        </label>
        <label class="check-line">
            <input type="checkbox" name="agreement" value="1" required> Saya memastikan data yang diisi benar.
        </label>
        <button class="btn primary full">Kirim pendaftaran</button>
        <p class="tiny muted">Data akan digunakan untuk keperluan administrasi event ini.</p>
    </form>
</section>
@endsection
