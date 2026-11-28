@extends('public.layout')
@section('title','Presensi Peserta')

@push('styles')
<style>
.attendance-wrap {
    max-width: 560px;
    margin: 72px auto;
    padding: 0 20px;
}
.attendance-card {
    margin-top: 22px;
    padding: 28px;
    background: var(--surface, #20242b);
    border: 1px solid var(--border, #39414c);
    border-radius: 18px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.attendance-field {
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
}
.attendance-field input {
    width: 100%;
    min-height: 50px;
    padding: 12px 16px;
    background: #252525;
    color: #eee;
    border: 1px solid #414141;
    border-radius: 10px;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: .18em;
    font-weight: 700;
    text-align: center;
}
.attendance-field input:focus {
    outline: none;
    border-color: var(--teal);
    box-shadow: 0 0 0 3px rgba(85, 214, 194, .12);
}
</style>
@endpush

@section('content')
<section class="attendance-wrap">
    <p class="eyebrow">GUEST CHECK-IN</p>
    <h1>Presensi Event</h1>
    <p class="muted">Masukkan kode presensi 8 karakter yang kamu terima setelah mendaftar event.</p>

    @if($errors->any())
        <div class="alert error" style="margin-top:16px;">
            @foreach($errors->all() as $error)
                <p style="margin:0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('public.attendance.submit') }}" class="attendance-card">
        @csrf
        <label class="attendance-field" for="code">
            <span>Kode Presensi</span>
            <input id="code" name="code" value="{{ old('code') }}" minlength="8" maxlength="8" required autocomplete="one-time-code" placeholder="A8K2P9QX">
        </label>
        <button class="btn primary full" type="submit">Konfirmasi Kehadiran</button>
    </form>
</section>
@endsection
