<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · EventHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="admin-body">
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('admin.dashboard') }}"><span
                    class="brand-mark">E</span><span>EventHub</span></a>
            <div class="nav-label">OVERVIEW</div>
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">◫ <span>Dashboard</span></a>
            <a class="nav-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}"
                href="{{ route('analytics.index') }}">⌁ <span>Analitik</span></a>
            <div class="nav-label">MANAJEMEN</div>
            <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}"
                href="{{ route('events.index') }}">▦ <span>Events</span></a>
            <a class="nav-link {{ request()->routeIs('admin.event-submissions.*') ? 'active' : '' }}"
                href="{{ route('admin.event-submissions.index') }}">◉ <span>Pengajuan Event</span></a>
            <a class="nav-link {{ request()->routeIs('pesertas.*') ? 'active' : '' }}"
                href="{{ route('pesertas.index') }}">♙ <span>Peserta</span></a>
            <a class="nav-link {{ request()->routeIs('pendaftarans.*') ? 'active' : '' }}"
                href="{{ route('pendaftarans.index') }}">☷ <span>Pendaftaran</span></a>
            <a class="nav-link {{ request()->routeIs('presensis.*') ? 'active' : '' }}"
                href="{{ route('presensis.index') }}">✓ <span>Presensi</span></a>
            <div class="nav-label">TOOLS</div>
            <a class="nav-link {{ request()->routeIs('data-management.*') ? 'active' : '' }}"
                href="{{ route('data-management.index') }}">⇅ <span>Import / Export</span></a>
            <div class="sidebar-bottom"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <div class="user-meta"><strong>{{ auth()->user()->name }}</strong><small>Super Admin</small></div>
                <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="icon-button"
                        title="Keluar">↗</button></form>
            </div>
        </aside>
        <main class="main-area">
            <header class="topbar">
                <button class="icon-button menu-toggle" type="button" data-sidebar-toggle
                    aria-label="Buka navigasi">☰</button>
                <form class="global-search" action="{{ route('search.index') }}" method="GET"><span>⌕</span><input
                        name="q" value="{{ request('q') }}"
                        placeholder="Cari event, peserta, pendaftaran..."><kbd>Enter</kbd></form>
                <button type="button" class="btn outline small" data-open-guide title="Panduan Admin">? Panduan</button>
                <a class="public-link" href="{{ route('home') }}" target="_blank" rel="noopener">Lihat website ↗</a>
            </header>
            <section class="page-content">
                @if(session('success'))
                <div class="alert success">{{ session('success') }}</div>@endif
                @if(session('error'))
                <div class="alert error">{{ session('error') }}</div>@endif
                @yield('content')
            </section>
        </main>
    </div>
    @include('components.onboarding-guide', [
        'storageKey' => 'eventhub_admin_guide_seen',
        'steps' => [
            [
                'badge' => 'DASHBOARD & ANALITIK',
                'title' => 'Pantau Ringkasan & Tren',
                'desc' => 'Gunakan Dashboard untuk melihat statistik cepat dan Analitik untuk memantau tren pendaftaran, status pembayaran, serta grafik per event.',
                'selector' => '.sidebar a[href*="analytics"]',
            ],
            [
                'badge' => 'EVENTS',
                'title' => 'Kelola Event & Titik Peta',
                'desc' => 'Buat atau edit event melalui menu Events. Kamu dapat mencari lokasi langsung di peta interaktif, mengunggah poster, dan mengatur kuota.',
                'selector' => '.sidebar a[href*="/events"]',
            ],
            [
                'badge' => 'MODERASI',
                'title' => 'Tinjau Pengajuan Event Tamu',
                'desc' => 'Buka menu Pengajuan Event untuk menyetujui atau menolak event yang diajukan oleh pengunjung sebelum tampil di halaman publik.',
                'selector' => '.sidebar a[href*="event-submissions"]',
            ],
            [
                'badge' => 'PENCARIAN',
                'title' => 'Pencarian Global',
                'desc' => 'Cari event, peserta, atau data pendaftaran dengan cepat dari kolom pencarian ini.',
                'selector' => '.global-search',
            ],
            [
                'badge' => 'PANDUAN',
                'title' => 'Buka Ulang Kapan Saja',
                'desc' => 'Klik tombol Panduan ini kapan saja jika kamu ingin mengulang tur fitur Admin EventHub.',
                'selector' => '[data-open-guide]',
            ],
        ],
    ])
    @stack('scripts')
</body>

</html>