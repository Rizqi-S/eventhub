@extends('layouts.admin')

@section('title', 'Tambah Presensi')

@section('content')
<div class="legacy-module-page">
    <div class="header">
        <div>
            <h1>Tambah Presensi</h1>
            <p>Tambahkan catatan presensi kehadiran peserta EventHub.</p>
        </div>

        <a href="{{ route('presensis.index') }}" class="btn-back">
            Kembali
        </a>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="alert error">
            <strong>Periksa kembali input berikut:</strong>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if($pendaftarans->isEmpty())
        <div class="table-card">
            <div class="empty">
                <h3 style="margin: 0 0 8px; font-size: 18px; color: var(--text);">Belum Ada Pendaftaran</h3>
                <p style="margin: 0 0 16px; color: var(--muted); font-size: 13px;">Belum ada data pendaftaran yang dapat dibuatkan presensi.</p>
                <a href="{{ route('presensis.index') }}" class="btn-cancel">
                    Kembali
                </a>
            </div>
        </div>
    @else
        <div class="presensi-card">
            <form action="{{ route('presensis.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="pendaftaran_id">Peserta dan Event</label>
                    <select name="pendaftaran_id" id="pendaftaran_id" required>
                        <option value="">-- Pilih Pendaftaran --</option>
                        @foreach($pendaftarans as $pendaftaran)
                            <option value="{{ $pendaftaran->id }}" {{ old('pendaftaran_id') == $pendaftaran->id ? 'selected' : '' }}>
                                {{ data_get($pendaftaran, 'peserta.nama', 'Peserta tidak ditemukan') }}
                                -
                                {{ data_get($pendaftaran, 'event.nama_event', 'Event tidak ditemukan') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="waktu_hadir">Waktu Hadir</label>
                    <input
                        type="datetime-local"
                        name="waktu_hadir"
                        id="waktu_hadir"
                        value="{{ old('waktu_hadir') }}">
                </div>

                <div class="form-group">
                    <label for="status_kehadiran">Status Kehadiran</label>
                    <select name="status_kehadiran" id="status_kehadiran" required>
                        @foreach(['belum_hadir', 'hadir', 'izin', 'sakit', 'alpa'] as $status)
                            <option value="{{ $status }}" {{ old('status_kehadiran', 'belum_hadir') == $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="actions">
                    <button type="submit" class="btn-save">
                        Simpan Presensi
                    </button>
                    <a href="{{ route('presensis.index') }}" class="btn-cancel">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
