<?php

require __DIR__ . '/../vendor/autoload.php';

// --- VERCEL SPECIFIC STORAGE CONFIGURATION ---
$storagePath = '/tmp/storage';

// Create necessary directories in /tmp
$directories = [
    "$storagePath/framework/views",
    "$storagePath/framework/cache/data",
    "$storagePath/framework/sessions",
    "$storagePath/logs",
    "$storagePath/bootstrap/cache",
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Tell Laravel to use this new path
putenv("APP_STORAGE=$storagePath");
$_ENV['APP_STORAGE'] = $storagePath;
$_SERVER['APP_STORAGE'] = $storagePath;
// ----------------------------------------------

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Instruct the application to use the custom storage path
if (env('APP_STORAGE')) {
    $app->useStoragePath(env('APP_STORAGE'));
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);