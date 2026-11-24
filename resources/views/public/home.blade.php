@extends('public.layout')
@section('title', 'Temukan Event')

@section('content')
<section class="hero hero-upgraded">
    <div class="hero-glow" aria-hidden="true"></div>
    <p class="eyebrow">DISCOVER · CONNECT · EXPERIENCE</p>
    <h1>Temukan event.<br><span>Buat pengalaman.</span></h1>
    <p class="hero-copy">Jelajahi seminar, workshop, kompetisi, dan kegiatan menarik. Temukan pengalaman berikutnya bersama EventHub.</p>
    <form class="hero-search" action="{{ route('public.events.index') }}" method="GET">
        <span class="search-symbol" aria-hidden="true">⌕</span>
        <input name="q" type="search" placeholder="Cari nama event atau lokasi..." aria-label="Cari event">
        <button class="btn primary" type="submit">Jelajahi Event <span aria-hidden="true">→</span></button>
    </form>
    <div class="hero-meta">
        <span>Temukan kegiatan menarik</span>
        <span>•</span>
        <span>Daftar dengan mudah</span>
        <span>•</span>
        <span>Tanpa akun pengunjung</span>
    </div>
</section>

<section class="public-section home-events-section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">UPCOMING EVENTS</p>
            <h2>Event pilihan</h2>
            <p class="section-subtitle">Pilihan kegiatan yang bisa kamu ikuti berikutnya.</p>
        </div>
        <a class="text-link" href="{{ route('public.events.index') }}">Lihat semua event <span aria-hidden="true">→</span></a>
    </div>

    <div class="event-grid event-grid-upgraded">
        @forelse($events as $event)
            <article class="event-card event-card-upgraded">
                <a class="event-card-media" href="{{ route('public.events.show', $event) }}" aria-label="Lihat {{ $event->nama_event }}">
                    @if($event->poster_url)
                        <img src="{{ $event->poster_url }}" alt="Poster {{ $event->nama_event }}" loading="lazy">
                    @else
                        <div class="event-card-placeholder"><span>EVENTHUB</span><strong>{{ Str::limit($event->nama_event, 42) }}</strong></div>
                    @endif
                    <span class="event-card-date-badge">{{ $event->tanggal_mulai->format('d M Y') }}</span>
                </a>
                <div class="event-card-content">
                    <div class="event-card-kicker">
                        <span class="event-tag">{{ str_replace('_', ' ', ucfirst($event->status_event)) }}</span>
                        <span class="event-price">{{ (float) $event->biaya > 0 ? 'Rp '.number_format((float) $event->biaya, 0, ',', '.') : 'Gratis' }}</span>
                    </div>
                    <h3><a href="{{ route('public.events.show', $event) }}">{{ $event->nama_event }}</a></h3>
                    <p class="event-card-description">{{ Str::limit(strip_tags($event->deskripsi ?? ''), 115) }}</p>
                    <div class="event-card-location"><span aria-hidden="true">⌖</span>{{ $event->lokasi }}</div>
                    <a class="event-card-cta" href="{{ route('public.events.show', $event) }}">Lihat detail event <span aria-hidden="true">→</span></a>
                </div>
            </article>
        @empty
            <div class="empty-state event-empty-state">
                <span class="empty-state-mark" aria-hidden="true">✦</span>
                <h3>Belum ada event tersedia</h3>
                <p>Event baru akan muncul di sini setelah dipublikasikan oleh administrator.</p>
                <a class="btn outline" href="{{ route('public.event-submissions.create') }}">Ajukan event</a>
            </div>
        @endforelse
    </div>
</section>

<section class="submission-banner">
    <div>
        <p class="eyebrow">PUNYA KEGIATAN?</p>
        <h2>Bagikan event-mu di EventHub.</h2>
        <p>Ajukan kegiatanmu dan tunggu proses peninjauan dari administrator.</p>
    </div>
    <a class="btn primary" href="{{ route('public.event-submissions.create') }}">Ajukan Event <span aria-hidden="true">→</span></a>
</section>
@endsection
