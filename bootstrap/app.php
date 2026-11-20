<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\EnsureSuperAdmin;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn (Request $request) => route('admin.login'));
        $middleware->redirectUsersTo(fn (Request $request) => route('admin.dashboard'));
        $middleware->alias(['superadmin' => EnsureSuperAdmin::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

// --- TAMBAHKAN KODE INI KHUSUS UNTUK VERCEL ---
$isRunningOnVercel = isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL']);

if ($isRunningOnVercel) {
    $app->useStoragePath('/tmp/storage');
    $directories = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
        '/tmp/storage/bootstrap/cache',
    ];
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
// ----------------------------------------------

return $app;