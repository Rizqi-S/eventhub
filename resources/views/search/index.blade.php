
@extends('layouts.admin')

@section('title', 'Hasil Pencarian')

@section('content')
<div class="page-header">
    <h1>Hasil Pencarian</h1>
    <p>
        Kata kunci:
        <strong>{{ $q ?: 'Belum ada pencarian' }}</strong>
    </p>
</div>

@if($q === '')
    <p>Masukkan kata kunci pada kolom pencarian di bagian atas.</p>
@else
    <section class="search-results">
        <h2>Events ({{ $events->count() }})</h2>

        @forelse($events as $event)
            <p>
                <a href="{{ route('events.edit', $event->id) }}">
                    {{ $event->nama_event }}
                </a>
                — {{ $event->lokasi }}
            </p>
        @empty
            <p>Tidak ada event ditemukan.</p>
        @endforelse

        <hr>

        <h2>Peserta ({{ $pesertas->count() }})</h2>

        @forelse($pesertas as $peserta)
            <p>
                <a href="{{ route('pesertas.edit', $peserta->id) }}">
                    {{ $peserta->nama }}
                </a>
                — {{ $peserta->email }}
            </p>
        @empty
            <p>Tidak ada peserta ditemukan.</p>
        @endforelse

        <hr>

        <h2>Pendaftaran ({{ $pendaftarans->count() }})</h2>

        @forelse($pendaftarans as $pendaftaran)
            <p>
                Kode: {{ $pendaftaran->attendance_code ?? '-' }}<br>
                Peserta: {{ $pendaftaran->peserta?->nama ?? '-' }}<br>
                Event: {{ $pendaftaran->event?->nama_event ?? '-' }}<br>
                Status: {{ $pendaftaran->status_pembayaran }}
            </p>
        @empty
            <p>Tidak ada pendaftaran ditemukan.</p>
        @endforelse
    </section>
@endif
@endsection