@extends('layouts.admin')

@section('title', 'Tinjau Pengajuan Event')

@section('content')
    <div class="legacy-module-page">

        <div class="header">
            <div>
                <h1>Tinjau Pengajuan</h1>
                <p>Periksa informasi dan sumber event sebelum mengambil keputusan.</p>
            </div>

            <a class="btn-edit" href="{{ route('events.edit', $event) }}">
                Edit Data Event
            </a>
        </div>

        <div class="table-card" style="padding:24px">

            <h2>{{ $event->nama_event }}</h2>

            @if($event->poster_url)
                <img src="{{ $event->poster_url }}" alt="Poster {{ $event->nama_event }}"
                    style="max-width:320px;max-height:240px;object-fit:contain">
            @endif

            <p>
                <strong>Jadwal:</strong>
                {{ $event->tanggal_mulai?->format('d M Y H:i') }}

                @if($event->tanggal_selesai)
                    – {{ $event->tanggal_selesai->format('d M Y H:i') }}
                @endif
            </p>

            <p>
                <strong>Lokasi:</strong>
                {{ $event->lokasi }}
            </p>

            <p>
                <strong>Kuota:</strong>
                {{ $event->kuota }}

                ·

                <strong>Biaya:</strong>
                Rp {{ number_format((float) $event->biaya, 0, ',', '.') }}
            </p>

            <p>
                <strong>Kontak:</strong>
                {{ $event->contact_name }}
                · {{ $event->contact_phone }}
                · {{ $event->contact_email }}
            </p>

            <p>
                <strong>Sumber:</strong>

                <a href="{{ $event->source_url }}" target="_blank" rel="noopener noreferrer">
                    {{ $event->source_url }}
                </a>
            </p>

            <hr>

            <h3>Deskripsi</h3>

            <div>
                {!! app(\App\Services\RichTextSanitizer::class)->clean($event->deskripsi) !!}
            </div>

            <hr>

            <h3>Data Pengaju</h3>

            <p>
                {{ $event->submitted_by_name }}
                · {{ $event->submitted_by_email }}
                · {{ $event->submitted_by_phone }}
            </p>

            @if($event->review_notes)
                <p>
                    <strong>Catatan sebelumnya:</strong>
                    {{ $event->review_notes }}
                </p>
            @endif

            @if($event->submission_status === 'pending')

                <div style="display:flex;gap:18px;flex-wrap:wrap;margin-top:24px">

                    <form method="POST" action="{{ route('admin.event-submissions.approve', $event) }}"
                        onsubmit="return confirm('Setujui dan terbitkan event ini?')">
                        @csrf

                        <button class="btn primary" type="submit">
                            Setujui & Terbitkan
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.event-submissions.reject', $event) }}"
                        style="min-width:min(100%,360px)">
                        @csrf

                        <label class="field">
                            <span>Alasan penolakan (wajib)</span>

                            <textarea name="review_notes" rows="3" required maxlength="2000"></textarea>
                        </label>

                        @error('review_notes')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                        <button class="btn-delete" type="submit" style="margin-top:10px">
                            Tolak Pengajuan
                        </button>
                    </form>

                </div>

            @else

                <p>
                    <strong>Status:</strong>
                    {{ ucfirst($event->submission_status) }}
                </p>

            @endif

        </div>
    </div>
@endsection