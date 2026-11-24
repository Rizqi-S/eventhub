@extends('layouts.admin')

@section('title', 'Pendaftaran')

@section('content')
<div class="legacy-module-page">
<div class="header">
            <h1>Edit Pendaftaran</h1>
            <p>Perbarui informasi pendaftaran event.</p>
        </div>

        <div class="form-card">

            <form action="{{ route('pendaftarans.update', ['id' => $pendaftaran->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Tanggal Daftar</label>
                    <input type="date" name="tanggal_daftar"
                        value="{{ old('tanggal_daftar', $pendaftaran->tanggal_daftar) }}"
                        required>
                    @error('tanggal_daftar')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Nama Peserta</label>
                    <select name="peserta_id" required>
                        @foreach ($pesertas as $peserta)
                            <option value="{{ $peserta->id }}"
                                {{ old('peserta_id', $pendaftaran->peserta_id) == $peserta->id ? 'selected' : '' }}>
                                {{ $peserta->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('peserta_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Nama Event</label>
                    <select name="event_id" required>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}"
                                {{ old('event_id', $pendaftaran->event_id) == $event->id ? 'selected' : '' }}>
                                {{ $event->nama_event }}
                            </option>
                        @endforeach
                    </select>
                    @error('event_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Status Pembayaran</label>
                    <select name="status_pembayaran" required>
                        <option value="belum_bayar" {{ old('status_pembayaran', $pendaftaran->status_pembayaran) == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar (15 menit)</option>
                        <option value="lunas" {{ old('status_pembayaran', $pendaftaran->status_pembayaran) == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="kedaluwarsa" {{ old('status_pembayaran', $pendaftaran->status_pembayaran) == 'kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                        <option value="gratis" {{ old('status_pembayaran', $pendaftaran->status_pembayaran) == 'gratis' ? 'selected' : '' }}>Gratis</option>
                    </select>
                </div>

                <div class="actions">
                    <button type="submit">Simpan Perubahan</button>
                    <a href="{{ route('pendaftarans.index') }}" class="back">Kembali</a>
                </div>

            </form>
</div>
@endsection

