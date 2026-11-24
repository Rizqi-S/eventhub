@extends('public.layout')
@section('title', 'Jelajahi Event')

@section('content')
<section class="directory-hero">
    <p class="eyebrow">EVENT DIRECTORY</p>
    <h1>Jelajahi event</h1>
    <p>Temukan kegiatan yang sesuai dengan minat dan jadwalmu.</p>
    <form class="filter-bar filter-bar-upgraded" action="{{ route('public.events.index') }}" method="GET">
        <span aria-hidden="true">⌕</span>
        <input name="q" type="search" value="{{ request('q') }}" placeholder="Cari nama event atau lokasi..." aria-label="Cari nama event atau lokasi">
        @if(request('q'))
            <a class="filter-reset" href="{{ route('public.events.index') }}">Reset</a>
        @endif
        <button class="btn primary" type="submit">Cari Event</button>
    </form>
    @if(request('q'))
        <p class="search-result-label">Hasil pencarian untuk <strong>“{{ request('q') }}”</strong></p>
    @endif
</section>

<section class="public-section directory-section">
    <div class="directory-heading">
        <div>
            <h2>Daftar event</h2>
            <p class="section-subtitle">{{ $events->total() }} event ditemukan</p>
        </div>
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
                <span class="empty-state-mark" aria-hidden="true">⌕</span>
                <h3>{{ request('q') ? 'Event tidak ditemukan' : 'Belum ada event tersedia' }}</h3>
                <p>{{ request('q') ? 'Coba kata kunci lain atau periksa kembali ejaan pencarianmu.' : 'Silakan kembali lagi nanti untuk melihat event terbaru.' }}</p>
                @if(request('q'))
                    <a class="btn outline" href="{{ route('public.events.index') }}">Tampilkan semua event</a>
                @endif
            </div>
        @endforelse
    </div>

    @if($events->hasPages())
        <div class="pagination-wrap">{{ $events->links() }}</div>
    @endif
</section>
@endsection
