<?php

require __DIR__ . '/../vendor/autoload.php';

// 1. Create the required storage structure in Vercel's /tmp directory
$storagePath = '/tmp/storage';
$directories = [
    "$storagePath/app/public",
    "$storagePath/framework/cache/data",
    "$storagePath/framework/sessions",
    "$storagePath/framework/testing",
    "$storagePath/framework/views",
    "$storagePath/logs",
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Set environment variables to route storage to /tmp
putenv("APP_STORAGE=$storagePath");
$_ENV['APP_STORAGE'] = $storagePath;
$_SERVER['APP_STORAGE'] = $storagePath;

// Safely route the compiled views path using env vars instead of the container
$viewPath = "$storagePath/framework/views";
putenv("VIEW_COMPILED_PATH=$viewPath");
$_ENV['VIEW_COMPILED_PATH'] = $viewPath;
$_SERVER['VIEW_COMPILED_PATH'] = $viewPath;

// 3. Boot the application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Force bind the storage path
$app->useStoragePath($storagePath);

// 5. Handle the request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);