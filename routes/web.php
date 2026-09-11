<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VoterController;
use App\Http\Controllers\Admin\CandidateGroupController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\VoteAccessController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PengumumanController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])
    ->name('home');

Route::get('/pengumuman', [PengumumanController::class, 'index'])
    ->name('pengumuman.index');

/*
|--------------------------------------------------------------------------
| Authenticated User
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Audit Logs
    |--------------------------------------------------------------------------
    | Hanya super_admin yang boleh melihat riwayat aktivitas.
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])
            ->name('audit-logs.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Voter Management
    |--------------------------------------------------------------------------
    | Semua role yang login boleh mengelola pemilih.
    |--------------------------------------------------------------------------
    */

    Route::get('/voters', [VoterController::class, 'index'])
        ->name('voters.index');

    Route::post('/voters/import', [VoterController::class, 'import'])
        ->middleware('throttle:10,1')
        ->name('voters.import');

    Route::get('/voters/export', [VoterController::class, 'export'])
        ->name('voters.export');

    Route::patch('/voters/{voter}/toggle', [VoterController::class, 'toggleStatus'])
        ->name('voters.toggle');

    Route::delete('/voters/{voter}', [VoterController::class, 'destroy'])
        ->name('voters.destroy');

    /*
    |--------------------------------------------------------------------------
    | Candidate Management
    |--------------------------------------------------------------------------
    | Semua role yang login boleh mengelola kandidat.
    |--------------------------------------------------------------------------
    */

    Route::get('/candidates', [CandidateGroupController::class, 'index'])
        ->name('candidates.index');

    Route::post('/candidates', [CandidateGroupController::class, 'store'])
        ->name('candidates.store');

    Route::post('/candidates/{candidateGroup}', [CandidateGroupController::class, 'update'])
        ->name('candidates.update');

    Route::delete('/candidates/{candidateGroup}', [CandidateGroupController::class, 'destroy'])
        ->name('candidates.destroy');

    /*
    |--------------------------------------------------------------------------
    | Election Management
    |--------------------------------------------------------------------------
    */

    // Melihat daftar election:
    // semua role yang login boleh melihat.
    Route::get('/elections', [ElectionController::class, 'index'])
        ->name('elections.index');

    /*
    |--------------------------------------------------------------------------
    | Sensitive Election Controls
    |--------------------------------------------------------------------------
    | Hanya super_admin dan admin yang boleh:
    | - membuka voting
    | - menutup voting
    | - mempublikasikan hasil
    |
    | Operator TIDAK boleh melakukan aksi tersebut.
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:super_admin,admin'])->group(function () {

        Route::patch('/elections/{election}/open', [ElectionController::class, 'open'])
            ->name('elections.open');

        Route::patch('/elections/{election}/close', [ElectionController::class, 'close'])
            ->name('elections.close');

        Route::patch('/elections/{election}/publish', [ElectionController::class, 'publish'])
            ->name('elections.publish');

        Route::get('/dashboard/export-results', [DashboardController::class, 'exportResults'])
            ->name('dashboard.export-results');
    });

    /*
    |--------------------------------------------------------------------------
    | Announcement Management
    |--------------------------------------------------------------------------
    | Semua role yang login boleh mengelola pengumuman.
    |--------------------------------------------------------------------------
    */

    Route::get('/announcements', [AnnouncementController::class, 'index'])
        ->name('announcements.index');

    Route::post('/announcements', [AnnouncementController::class, 'store'])
        ->name('announcements.store');

    Route::post('/announcements/{announcement}', [AnnouncementController::class, 'update'])
        ->name('announcements.update');

    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])
        ->name('announcements.destroy');
});

/*
|--------------------------------------------------------------------------
| Public Election Results
|--------------------------------------------------------------------------
*/

Route::get('/hasil/{type}', [HasilController::class, 'show'])
    ->name('hasil.show');

/*
|--------------------------------------------------------------------------
| Vote Access
|--------------------------------------------------------------------------
*/

Route::get('/vote/access', [VoteAccessController::class, 'show'])
    ->name('vote.access');

Route::post('/vote/access', [VoteAccessController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('vote.access.store');

/*
|--------------------------------------------------------------------------
| Voting Pages
|--------------------------------------------------------------------------
*/

Route::middleware('voter.session')->group(function () {

    Route::get('/vote/osis', [VoteController::class, 'osis'])
        ->name('vote.osis');

    Route::post('/vote/osis', [VoteController::class, 'storeOsis'])
        ->name('vote.osis.store');

    Route::get('/vote/mpk', [VoteController::class, 'mpk'])
        ->name('vote.mpk');

    Route::post('/vote/mpk', [VoteController::class, 'storeMpk'])
        ->name('vote.mpk.store');

    Route::get('/vote/done', [VoteController::class, 'done'])
        ->name('vote.done');
});

require __DIR__.'/settings.php';