@extends('public.layout')
@section('title',$event->nama_event)
@section('content')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>.public-event-poster{display:block;width:100%;max-height:460px;object-fit:contain;object-position:left center;margin:22px 0;border-radius:16px;background:#202020}.event-rich-description{line-height:1.75}.event-rich-description p{margin:0 0 12px}.event-rich-description ul,.event-rich-description ol{padding-left:24px;margin:10px 0}.event-rich-description a,.event-organizer-info a{color:var(--teal);text-decoration:underline}.event-organizer-info{margin-top:28px;padding:20px;border:1px solid var(--border);border-radius:14px}.event-organizer-info h2{margin-top:0}.public-event-map{height:330px;width:100%;border-radius:14px;overflow:hidden;margin-top:18px;background:#252525}.map-attribution-note{display:block;text-align:right;color:#9ca3af;font-size:10px;margin-top:6px}.map-attribution-note a{color:#9ca3af}</style>
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>document.addEventListener('DOMContentLoaded',()=>{const el=document.getElementById('public-event-map');if(!el||typeof L==='undefined')return;const point=[Number(el.dataset.latitude),Number(el.dataset.longitude)];const map=L.map(el,{scrollWheelZoom:false}).setView(point,15);L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'}).addTo(map);L.marker(point).addTo(map);});</script>
@endpush
<section class="public-section detail-layout">
    <article class="detail-main">
        <a class="text-link" href="{{ route('public.events.index') }}">← Semua event</a>~
        <p class="eyebrow">EVENT DETAIL</p>
        <h1>{{ $event->nama_event }}</h1>
        @if($event->poster_url)
            <img src="{{ $event->poster_url }}" alt="Poster {{ $event->nama_event }}" class="public-event-poster" loading="lazy">
        @endif
        <div class="lead event-rich-description">{!! app(\App\Services\RichTextSanitizer::class)->clean($event->deskripsi) !!}</div>
        <div class="detail-facts">
            <div>
                <small>TANGGAL</small>
                <strong>{{ $event->tanggal_mulai->format('d M Y, H:i') }}</strong>
            </div>
            <div>
                <small>LOKASI</small>
                <strong>{{ $event->lokasi }}</strong>
            </div>
            <div>
                <small>BIAYA</small>
                <strong>{{ $event->biaya > 0 ? 'Rp '.number_format($event->biaya,0,',','.') : 'Gratis' }}</strong>
            </div>
        </div>
        @if($event->contact_name || $event->contact_phone || $event->contact_email || $event->source_url)
            <section class="event-organizer-info">
                <h2>Informasi Penyelenggara</h2>
                @if($event->contact_name)<p><strong>Narahubung:</strong> {{ $event->contact_name }}</p>@endif
                @if($event->contact_phone)<p><strong>Telepon:</strong> <a href="tel:{{ preg_replace('/[^0-9+]/', '', $event->contact_phone) }}">{{ $event->contact_phone }}</a></p>@endif
                @if($event->contact_email)<p><strong>Email:</strong> <a href="mailto:{{ $event->contact_email }}">{{ $event->contact_email }}</a></p>@endif
                @if($event->source_url)<p><strong>Sumber informasi:</strong> <a href="{{ $event->source_url }}" target="_blank" rel="noopener noreferrer">Lihat sumber event ↗</a></p>@endif
            </section>
        @endif
        @if($event->latitude !== null && $event->longitude !== null)
            <h2 class="map-section-title">Lokasi Event</h2>
            <div id="public-event-map" class="public-event-map" data-latitude="{{ $event->latitude }}" data-longitude="{{ $event->longitude }}"></div>
            <div class="map-navigation-links" style="display:flex;gap:10px;flex-wrap:wrap;margin-top:12px">
                <a class="btn primary" href="https://www.google.com/maps/dir/?api=1&amp;destination={{ $event->latitude }},{{ $event->longitude }}">Buka Google Maps</a>
                <a class="btn outline" href="https://waze.com/ul?ll={{ $event->latitude }},{{ $event->longitude }}&amp;navigate=yes">Buka Waze</a>
            </div>
            <small class="map-attribution-note">© <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap contributors</a></small>
        @endif
    </article>
    <aside class="register-panel">
        <p class="eyebrow">REGISTRATION</p>
        <h2>{{ $remaining }} slot tersisa</h2>
        <p class="muted">Kuota tersedia: {{ $event->kuota }} peserta.</p>
        @if($remaining > 0)
            <a class="btn primary full" href="{{ route('public.events.register',$event) }}">Daftar sekarang</a>
        @else
            <button class="btn disabled full" disabled>Kuota penuh</button>
        @endif
        <p class="tiny muted">Pendaftaran tidak memerlukan akun.</p>
    </aside>
</section>
@endsection
