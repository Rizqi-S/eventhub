<?php

require __DIR__ . '/../vendor/autoload.php';

// 1. Create the required storage structure in Vercel's /tmp directory
$storagePath = '/tmp/storage';$directories = [
    "$storagePath/app/public",
    "$storagePath/framework/cache/data",
    "$storagePath/framework/sessions",
    "$storagePath/framework/testing",
    "$storagePath/framework/views",
    "$storagePath/logs",
];

foreach ($directories as$dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Set environment variables just in case
putenv("APP_STORAGE=$storagePath");
$_ENV['APP_STORAGE'] =$storagePath;
$_SERVER['APP_STORAGE'] =$storagePath;

// 3. Boot the application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. FORCE BIND the storage paths directly into the container
$app->useStoragePath($storagePath);

// Explicitly bind the view.compiled path to avoid the specific view error
$app['config']->set('view.compiled', "$storagePath/framework/views");

// 5. Handle the request
$kernel =$app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle($request = Illuminate\Http\Request::capture()
);

$response->send();