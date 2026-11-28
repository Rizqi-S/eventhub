<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#191919">
    <title>@yield('title', 'EventHub') &middot; EventHub</title>
    <meta name="description" content="Temukan kegiatan, seminar, workshop, dan event menarik bersama EventHub.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="public-body">
    <header class="public-nav">
        <a class="brand" href="{{ route('home') }}" aria-label="EventHub Beranda">
            <span class="brand-mark">E</span>
            <span>EventHub</span>
        </a>

        <nav aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-current' : '' }}">Beranda</a>
            <a href="{{ route('public.events.index') }}" class="{{ request()->routeIs('public.events.*') ? 'is-current' : '' }}">Jelajahi Event</a>
            <a href="{{ route('public.attendance.form') }}" class="{{ request()->routeIs('public.attendance.*') ? 'is-current' : '' }}">Presensi</a>
        </nav>

        <div class="public-nav-actions">
            <button type="button" class="btn outline" data-open-guide title="Panduan Penggunaan">? Panduan</button>
            <a class="public-submit-link" href="{{ route('public.event-submissions.create') }}">Ajukan Event <span aria-hidden="true">↗</span></a>
            <a class="btn outline public-admin-link" href="{{ route('admin.login') }}">Admin</a>
        </div>
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    <footer class="public-footer">
        <div class="footer-main">
            <div class="footer-brand-block">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-mark">E</span>
                    <span>EventHub</span>
                </a>
                <p>Temukan kegiatan, perluas koneksi, dan ciptakan pengalaman baru.</p>
            </div>
            <div class="footer-links">
                <strong>Jelajahi</strong>
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('public.events.index') }}">Semua Event</a>
                <a href="{{ route('public.event-submissions.create') }}">Ajukan Event</a>
            </div>
            <div class="footer-links">
                <strong>Layanan</strong>
                <a href="{{ route('public.attendance.form') }}">Presensi</a>
                <a href="{{ route('admin.login') }}">Admin</a>
            </div>
            <div class="footer-links">
                <strong>Kontak</strong>
                <span class="footer-contact-item">Contact Person: <strong>Rizqi S.</strong></span>
                <a class="footer-wa-link" href="https://wa.me/6285954177523" target="_blank" rel="noopener noreferrer">
                    Phone / WhatsApp: +62 859 5417 7523
                </a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} EventHub. All rights reserved.</span>
            <span>Dibuat untuk menghubungkan berbagai pengalaman.</span>
        </div>
    </footer>

    @include('components.onboarding-guide', [
        'storageKey' => 'eventhub_guest_guide_seen',
        'steps' => [
            [
                'badge' => 'SELAMAT DATANG',
                'title' => 'Jelajahi Event di EventHub',
                'desc' => 'Temukan berbagai seminar, workshop, dan kegiatan menarik melalui halaman Beranda atau menu Jelajahi Event.',
                'selector' => '.public-nav nav a[href*="/events"]',
            ],
            [
                'badge' => 'PRESENSI',
                'title' => 'Konfirmasi Kehadiran',
                'desc' => 'Setelah mendaftar, kamu akan menerima kode presensi 8 karakter. Gunakan kode tersebut di menu Presensi saat hari acara.',
                'selector' => '.public-nav nav a[href*="/presensi"]',
            ],
            [
                'badge' => 'KOMUNITAS',
                'title' => 'Ajukan Event Kamu Sendiri',
                'desc' => 'Punya acara yang ingin dipromosikan? Klik Ajukan Event di pojok kanan atas untuk mengirim detail acara ke tim EventHub.',
                'selector' => '.public-submit-link',
            ],
            [
                'badge' => 'PANDUAN',
                'title' => 'Buka Ulang Kapan Saja',
                'desc' => 'Klik tombol Panduan ini kapan saja jika kamu ingin melihat kembali langkah-langkah penggunaan EventHub.',
                'selector' => '[data-open-guide]',
            ],
        ],
    ])
    @stack('scripts')
</body>
</html>
