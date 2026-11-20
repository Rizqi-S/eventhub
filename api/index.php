<?php

// 1. Tentukan path storage khusus untuk Vercel
$storage = '/tmp';

// 2. Buat struktur folder yang dibutuhkan Laravel jika belum ada
$directories = [
    "$storage/framework/cache/data",
    "$storage/framework/sessions",
    "$storage/framework/views",
    "$storage/logs",
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
}

// 3. Forward request ke public/index.php Laravel
require __DIR__ . '/../public/index.php';