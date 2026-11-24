@extends('public.layout')
@section('title', $event->nama_event)

@section('content')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const element = document.getElementById('public-event-map');
    if (!element || typeof L === 'undefined') return;
    const point = [Number(element.dataset.latitude), Number(element.dataset.longitude)];
    if (!Number.isFinite(point[0]) || !Number.isFinite(point[1])) return;
    const map = L.map(element, { scrollWheelZoom: false }).setView(point, 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    L.marker(point).addTo(map);
});
</script>
@endpush

<section class="public-section event-detail-page">
    <a class="back-link" href="{{ route('public.events.index') }}"><span aria-hidden="true">←</span> Kembali ke semua event</a>

    <div class="event-detail-heading">
        <div>
            <p class="eyebrow">EVENT DETAIL</p>
            <h1>{{ $event->nama_event }}</h1>
            <div class="event-detail-subline">
                <span>{{ $event->lokasi }}</span>
                <span aria-hidden="true">·</span>
                <span>{{ $event->tanggal_mulai->format('d M Y') }}</span>
            </div>
        </div>
        <span class="event-tag event-detail-status">{{ str_replace('_', ' ', ucfirst($event->status_event)) }}</span>
    </div>

    @if($event->poster_url)
        <div class="event-detail-poster-wrap">
            <img src="{{ $event->poster_url }}" alt="Poster {{ $event->nama_event }}" class="event-detail-poster">
        </div>
    @endif

    <div class="event-detail-columns">
        <article class="event-detail-main">
            <section class="detail-content-panel">
                <p class="eyebrow">ABOUT THIS EVENT</p>
                <h2>Tentang event</h2>
                <div class="event-rich-description">{!! app(\App\Services\RichTextSanitizer::class)->clean($event->deskripsi ?? '') !!}</div>
            </section>

            <section class="detail-content-panel">
                <p class="eyebrow">EVENT INFORMATION</p>
                <h2>Informasi kegiatan</h2>
                <div class="event-facts-grid">
                    <div class="event-fact-item"><span class="fact-icon" aria-hidden="true">◷</span><div><small>TANGGAL MULAI</small><strong>{{ $event->tanggal_mulai->format('d M Y') }}</strong><span>{{ $event->tanggal_mulai->format('H:i') }} WIB</span></div></div>
                    @if($event->tanggal_selesai)
                        <div class="event-fact-item"><span class="fact-icon" aria-hidden="true">◷</span><div><small>TANGGAL SELESAI</small><strong>{{ $event->tanggal_selesai->format('d M Y') }}</strong><span>{{ $event->tanggal_selesai->format('H:i') }} WIB</span></div></div>
                    @endif
                    <div class="event-fact-item"><span class="fact-icon" aria-hidden="true">⌖</span><div><small>LOKASI</small><strong>{{ $event->lokasi }}</strong></div></div>
                    <div class="event-fact-item"><span class="fact-icon" aria-hidden="true">◈</span><div><small>BIAYA</small><strong>{{ (float) $event->biaya > 0 ? 'Rp '.number_format((float) $event->biaya, 0, ',', '.') : 'Gratis' }}</strong></div></div>
                    <div class="event-fact-item"><span class="fact-icon" aria-hidden="true">♙</span><div><small>KUOTA PESERTA</small><strong>{{ number_format($event->kuota, 0, ',', '.') }} orang</strong></div></div>
                </div>
            </section>

            @if($event->contact_name || $event->contact_phone || $event->contact_email || $event->source_url)
                <section class="detail-content-panel organizer-panel">
                    <p class="eyebrow">CONTACT</p>
                    <h2>Informasi penyelenggara</h2>
                    @if($event->contact_name)<p><strong>Narahubung</strong><span>{{ $event->contact_name }}</span></p>@endif
                    @if($event->contact_phone)<p><strong>Telepon</strong><a href="tel:{{ preg_replace('/[^0-9+]/', '', $event->contact_phone) }}">{{ $event->contact_phone }}</a></p>@endif
                    @if($event->contact_email)<p><strong>Email</strong><a href="mailto:{{ $event->contact_email }}">{{ $event->contact_email }}</a></p>@endif
                    @if($event->source_url)<p><strong>Sumber informasi</strong><a href="{{ $event->source_url }}" target="_blank" rel="noopener noreferrer">Kunjungi sumber event ↗</a></p>@endif
                </section>
            @endif

            @if($event->latitude !== null && $event->longitude !== null)
                <section class="detail-content-panel">
                    <p class="eyebrow">LOCATION</p>
                    <h2>Lokasi event</h2>
                    <div id="public-event-map" class="public-event-map" data-latitude="{{ $event->latitude }}" data-longitude="{{ $event->longitude }}"></div>
                    <div class="map-navigation-links">
                        <a class="btn outline" href="https://www.google.com/maps/dir/?api=1&amp;destination={{ $event->latitude }},{{ $event->longitude }}" target="_blank" rel="noopener noreferrer">Buka Google Maps ↗</a>
                        <a class="btn outline" href="https://waze.com/ul?ll={{ $event->latitude }},{{ $event->longitude }}&amp;navigate=yes" target="_blank" rel="noopener noreferrer">Buka Waze ↗</a>
                    </div>
                    <small class="map-attribution-note">© <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap contributors</a></small>
                </section>
            @endif
        </article>

        <aside class="register-panel register-panel-upgraded">
            <p class="eyebrow">REGISTRATION</p>
            <div class="registration-price">{{ (float) $event->biaya > 0 ? 'Rp '.number_format((float) $event->biaya, 0, ',', '.') : 'Gratis' }}</div>
            <div class="remaining-slots"><strong>{{ number_format($remaining, 0, ',', '.') }}</strong><span>slot tersisa</span></div>
            <div class="quota-track"><span style="width: {{ $event->kuota > 0 ? min(100, max(0, (($event->kuota - $remaining) / $event->kuota) * 100)) : 0 }}%"></span></div>
            <p class="muted">Dari total {{ number_format($event->kuota, 0, ',', '.') }} kuota peserta.</p>
            @if($remaining > 0)
                <a class="btn primary full" href="{{ route('public.events.register', $event) }}">Daftar sekarang <span aria-hidden="true">→</span></a>
            @else
                <button class="btn disabled full" disabled>Kuota penuh</button>
            @endif
            <p class="registration-note">Pendaftaran dilakukan secara online dan tidak memerlukan akun.</p>
        </aside>
    </div>
</section>
@endsection
