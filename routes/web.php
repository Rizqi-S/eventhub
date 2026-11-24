<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\SuperAdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataManagementController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\MidtransPaymentController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\GuestEventSubmissionController;
use App\Http\Controllers\AdminEventSubmissionController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
| Dapat diakses tanpa login.
*/

// Homepage dan event publik
Route::get('/', [PublicEventController::class, 'home'])
    ->name('home');

Route::get('/events', [PublicEventController::class, 'index'])
    ->name('public.events.index');

Route::get('/events/{event}', [PublicEventController::class, 'show'])
    ->name('public.events.show');

Route::get('/events/{event}/register', [PublicEventController::class, 'register'])
    ->name('public.events.register');

Route::post('/events/{event}/register', [PublicEventController::class, 'storeRegistration'])
    ->middleware('throttle:10,1')
    ->name('public.events.store');

// Konfirmasi pendaftaran
Route::get('/registration/confirmation/{token}', [PublicEventController::class, 'confirmation'])
    ->name('public.registration.confirmation');

// Presensi publik
Route::get('/presensi', [PublicEventController::class, 'attendanceForm'])
    ->name('public.attendance.form');

Route::post('/presensi', [PublicEventController::class, 'submitAttendance'])
    ->middleware('throttle:10,1')
    ->name('public.attendance.submit');

// Pembayaran Midtrans
Route::post('/registration/{token}/pay', [MidtransPaymentController::class, 'pay'])
    ->name('public.payment.pay');

Route::post('/midtrans/notification', [MidtransPaymentController::class, 'notification'])
    ->name('midtrans.notification');


/*
|--------------------------------------------------------------------------
| GUEST EVENT SUBMISSION
|--------------------------------------------------------------------------
| Pengunjung dapat mengajukan event tanpa login.
| Pengajuan akan masuk ke antrean moderasi admin.
*/

Route::get('/ajukan-event', [GuestEventSubmissionController::class, 'create'])
    ->name('public.event-submissions.create');

Route::post('/ajukan-event', [GuestEventSubmissionController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('public.event-submissions.store');

Route::get('/ajukan-event/berhasil', [GuestEventSubmissionController::class, 'success'])
    ->name('public.event-submissions.success');


/*
|--------------------------------------------------------------------------
| SUPER ADMIN AUTHENTICATION
|--------------------------------------------------------------------------
| Tidak menyediakan registrasi akun admin publik.
*/

Route::get('/admin/login', [SuperAdminAuthController::class, 'create'])
    ->middleware('guest')
    ->name('admin.login');

Route::post('/admin/login', [SuperAdminAuthController::class, 'store'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('admin.login.store');

Route::post('/admin/logout', [SuperAdminAuthController::class, 'destroy'])
    ->middleware(['auth', 'superadmin'])
    ->name('admin.logout');


/*
|--------------------------------------------------------------------------
| PRIVATE ADMIN ROUTES
|--------------------------------------------------------------------------
| Hanya Super Admin yang sudah login yang dapat mengakses.
*/

Route::middleware(['auth', 'superadmin'])
    ->prefix('admin')
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])
            ->name('admin.dashboard');

        // Analytics
        Route::get('/analytics', [AnalyticsController::class, 'index'])
            ->name('analytics.index');

        // Global Search
        Route::get('/search', [GlobalSearchController::class, 'index'])
            ->name('search.index');


        /*
        |--------------------------------------------------------------------------
        | DATA MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::prefix('data-management')
            ->name('data-management.')
            ->group(function () {

                Route::get('/', [DataManagementController::class, 'index'])
                    ->name('index');

                Route::post('/import', [DataManagementController::class, 'import'])
                    ->name('import');

                Route::get('/export/{type}/{format}', [DataManagementController::class, 'export'])
                    ->name('export');
            });


        /*
        |--------------------------------------------------------------------------
        | EVENT MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::resource('events', EventController::class);


        /*
        |--------------------------------------------------------------------------
        | GUEST EVENT MODERATION
        |--------------------------------------------------------------------------
        | Admin dapat melihat, menyetujui, dan menolak pengajuan event.
        */

        Route::prefix('event-submissions')
            ->name('admin.event-submissions.')
            ->group(function () {

                Route::get('/', [AdminEventSubmissionController::class, 'index'])
                    ->name('index');

                Route::get('/{event}', [AdminEventSubmissionController::class, 'show'])
                    ->name('show');

                Route::post('/{event}/approve', [AdminEventSubmissionController::class, 'approve'])
                    ->name('approve');

                Route::post('/{event}/reject', [AdminEventSubmissionController::class, 'reject'])
                    ->name('reject');
            });


        /*
        |--------------------------------------------------------------------------
        | PESERTA MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::resource('pesertas', PesertaController::class)
            ->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | PRESENSI MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::resource('presensis', PresensiController::class);


        /*
        |--------------------------------------------------------------------------
        | PENDAFTARAN MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::get('/pendaftarans', [PendaftaranController::class, 'index'])
            ->name('pendaftarans.index');

        Route::get('/pendaftarans/create', [PendaftaranController::class, 'create'])
            ->name('pendaftarans.create');

        Route::post('/pendaftarans', [PendaftaranController::class, 'store'])
            ->name('pendaftarans.store');

        Route::get('/pendaftarans/{id}/edit', [PendaftaranController::class, 'edit'])
            ->name('pendaftarans.edit');

        Route::put('/pendaftarans/{id}', [PendaftaranController::class, 'update'])
            ->name('pendaftarans.update');

        Route::delete('/pendaftarans/{id}', [PendaftaranController::class, 'destroy'])
            ->name('pendaftarans.destroy');
    });