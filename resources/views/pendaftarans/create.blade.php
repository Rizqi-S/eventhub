@extends('layouts.admin')

@section('title', 'Pendaftaran')

@section('content')
<div class="legacy-module-page">
<div class="topbar">

            <h1>Tambah Pendaftaran</h1>
        </div>

        @if($pesertas->isEmpty() || $events->isEmpty())
            <section class="form-card">
                <div class="form-heading">
                    <h2>Belum bisa membuat pendaftaran</h2>
                    <p>Tambahkan data yang dibutuhkan terlebih dahulu. Halaman ini tetap bisa dibuka meskipun database masih kosong.</p>
                </div>
                @if($pesertas->isEmpty())
                    <p>Belum ada data peserta. <a href="{{ route('pesertas.create') }}">Tambah peserta</a> terlebih dahulu.</p>
                @endif
                @if($events->isEmpty())
                    <p>Belum ada data event. <a href="{{ route('events.create') }}">Tambah event</a> terlebih dahulu.</p>
                @endif
                <div class="form-actions"><a href="{{ route('pendaftarans.index') }}" class="btn btn-gray">Kembali ke Pendaftaran</a></div>
            </section>
        @else
        <section class="form-card">

            <div class="form-heading">
                <h2>Formulir Pendaftaran Event</h2>
                <p>Lengkapi seluruh informasi di bawah ini.</p>
            </div>

            @if ($errors->any())
                <div class="alert-error">
                    <strong>Terjadi kesalahan:</strong>
                    <ul style="margin: 8px 0 0 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pendaftarans.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Tanggal Daftar</label>
                    <input
                        type="date"
                        name="tanggal_daftar"
                        value="{{ old('tanggal_daftar') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Peserta</label>
                    <select name="peserta_id" required>
                        <option value="">-- Pilih Peserta --</option>

                        @foreach ($pesertas as $peserta)
                            <option
                                value="{{ $peserta->id }}"
                                {{ old('peserta_id') == $peserta->id ? 'selected' : '' }}
                            >
                                {{ $peserta->nama }}
                            </option>
                        @endforeach
                    </select>
                    <small>Pilih peserta yang akan mengikuti event.</small>
                </div>

                <div class="form-group">
                    <label>Event</label>
                    <select name="event_id" required>
                        <option value="">-- Pilih Event --</option>

                        @foreach ($events as $event)
                            <option
                                value="{{ $event->id }}"
                                {{ old('event_id') == $event->id ? 'selected' : '' }}
                            >
                                {{ $event->nama_event }}
                            </option>
                        @endforeach
                    </select>
                    <small>Pilih event yang ingin diikuti peserta.</small>
                </div>

                <div class="form-group">
                    <label>Status Pembayaran</label>
                    <select name="status_pembayaran" required>
                        <option value="belum_bayar" {{ old('status_pembayaran', 'belum_bayar') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar (15 menit)</option>
                        <option value="lunas" {{ old('status_pembayaran') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="kedaluwarsa" {{ old('status_pembayaran') == 'kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                        <option value="gratis" {{ old('status_pembayaran') == 'gratis' ? 'selected' : '' }}>Gratis</option>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="{{ route('pendaftarans.index') }}"
                       class="btn btn-gray">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Simpan Pendaftaran
                    </button>
                </div>

            </form>
        </section>
        @endif
</div>
@endsection

