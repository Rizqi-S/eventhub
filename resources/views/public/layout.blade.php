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
            <a href="{{ route('public.event-submissions.create') }}" class="{{ request()->routeIs('public.event-submissions.*') ? 'is-current' : '' }}">Ajukan Event</a>
        </nav>

        <div class="public-nav-actions">
            <button type="button" class="btn outline" data-open-guide title="Guidelines" aria-label="Guidelines" style="display:inline-flex;align-items:center;justify-content:center;padding:8px 10px;">
                <img src="https://unpkg.com/lucide-static@0.542.0/icons/book-open.svg" alt="Guidelines" width="16" height="16" style="filter:brightness(0) invert(1);">
            </button>
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
                'badge' => 'JELAJAHI EVENT',
                'title' => 'Temukan & Filter Event',
                'desc' => 'Buka menu Jelajahi Event untuk melihat seluruh acara yang tersedia. Kamu bisa mencari event, melihat tanggal, lokasi peta, kuota tersisa, serta biaya pendaftaran.',
                'selector' => '.public-nav nav a[href*="/events"]',
            ],
            [
                'badge' => 'PENDAFTARAN & PEMBAYARAN',
                'title' => 'Cara Mendaftar Event',
                'desc' => 'Di halaman detail event, klik Daftar Sekarang lalu isi Nama Lengkap, Email aktif, Nomor HP (8–15 digit), dan Instansi. Untuk event berbayar, lanjutkan pembayaran langsung melalui Midtrans.',
                'selector' => '.public-nav nav a[href*="/events"]',
            ],
            [
                'badge' => 'PRESENSI KEHADIRAN',
                'title' => 'Gunakan Kode Presensi 8 Karakter',
                'desc' => 'Setelah pendaftaran berhasil, kamu akan mendapatkan kode presensi unik 8 karakter. Buka menu Presensi saat hari acara dan masukkan kode tersebut untuk mengonfirmasi kehadiran.',
                'selector' => '.public-nav nav a[href*="/presensi"]',
            ],
            [
                'badge' => 'AJUKAN EVENT',
                'title' => 'Promosikan Event Komunitasmu',
                'desc' => 'Klik Ajukan Event untuk mengirim detail acaramu: isi Nama Event, pilih titik lokasi di peta interaktif, unggah poster, tulis deskripsi, lengkapi kontak penyelenggara & data pengaju. Tim Admin akan meninjau sebelum dipublikasikan.',
                'selector' => '.public-nav nav a[href*="/ajukan-event"]',
            ],
            [
                'badge' => 'GUIDELINES',
                'title' => 'Buka Ulang Panduan Kapan Saja',
                'desc' => 'Klik tombol Guidelines ini kapan saja jika kamu ingin melihat kembali panduan lengkap penggunaan EventHub.',
                'selector' => '[data-open-guide]',
            ],
        ],
    ])
    @stack('scripts')
</body>
</html>
