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
                <button type="button" class="btn outline small" data-open-guide title="Admin Guide" style="display:inline-flex;align-items:center;gap:6px;">
                    <img src="https://unpkg.com/lucide-static@0.542.0/icons/book-user.svg" alt="" width="14" height="14" style="filter:brightness(0) invert(1);">
                    <span>Admin Guide</span>
                </button>
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
                'badge' => 'DASHBOARD',
                'title' => 'Ringkasan Aktivitas & Quick Actions',
                'desc' => 'Dashboard menampilkan total Event, Peserta, Pendaftaran, Presensi, tombol Quick Actions untuk input cepat, serta grafik distribusi presensi dan pendaftaran per event.',
                'selector' => '.sidebar a[href$="/admin"]',
            ],
            [
                'badge' => 'ANALITIK',
                'title' => 'Pantau Tren & Performa Event',
                'desc' => 'Di menu Analitik, gunakan filter periode (7/30/90/365 hari) untuk melihat grafik tren pendaftaran harian, status pembayaran, presensi, dan grafik batang per event.',
                'selector' => '.sidebar a[href*="analytics"]',
            ],
            [
                'badge' => 'MANAJEMEN EVENT',
                'title' => 'Buat, Edit, Peta & Poster Event',
                'desc' => 'Buka menu Events untuk membuat atau mengedit event. Kamu bisa mencari alamat dan menggeser pin peta (Leaflet), mengunggah poster (Cloudinary), menulis deskripsi dengan editor teks, serta mengatur kuota, biaya, dan status event (Draft/Aktif/Berlangsung/Selesai).',
                'selector' => '.sidebar a[href*="/events"]',
            ],
            [
                'badge' => 'MODERASI TAMU',
                'title' => 'Tinjau Pengajuan Event Tamu',
                'desc' => 'Menu Pengajuan Event menampung acara yang dikirim pengunjung. Periksa detail acara, kontak, dan link sumber resmi, lalu klik Setujui untuk menerbitkan atau Tolak disertai catatan.',
                'selector' => '.sidebar a[href*="event-submissions"]',
            ],
            [
                'badge' => 'OPERASIONAL',
                'title' => 'Kelola Peserta, Pendaftaran & Presensi',
                'desc' => 'Gunakan menu Peserta, Pendaftaran, dan Presensi untuk menambah/mengedit data peserta, memverifikasi status pendaftaran & pembayaran, serta mencatat kehadiran peserta.',
                'selector' => '.sidebar a[href*="pesertas"]',
            ],
            [
                'badge' => 'TOOLS & PENCARIAN',
                'title' => 'Import / Export & Pencarian Global',
                'desc' => 'Menu Import / Export memungkinkan ekspor laporan (PDF/Excel/CSV) dan impor data massal. Gunakan juga kolom Pencarian Global di topbar untuk mencari event atau peserta secara instan.',
                'selector' => '.sidebar a[href*="data-management"]',
            ],
            [
                'badge' => 'ADMIN GUIDE',
                'title' => 'Buka Ulang Panduan Kapan Saja',
                'desc' => 'Klik tombol Admin Guide ini kapan saja jika kamu ingin mengulang panduan lengkap pengelolaan EventHub.',
                'selector' => '[data-open-guide]',
            ],
        ],
    ])
    @stack('scripts')
</body>

</html>