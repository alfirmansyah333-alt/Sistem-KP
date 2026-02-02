<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mahasiswa\MahasiswaController;
use App\Http\Controllers\Dosen\DosenController;
use App\Http\Controllers\Koor\KoordinatorController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PerusahaanController;

/**
 * ============================================================================
 * ROOT & AUTH ROUTES
 * ============================================================================
 */

/**
 * Root route: Redirect ke login atau dashboard sesuai role
 */
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole('mahasiswa')) {
            return redirect()->route('mahasiswa.dashboard');
        } elseif ($user->hasRole('dosen')) {
            return redirect()->route('dosen.bimbingan');
        } elseif ($user->hasRole('koor')) {
            return redirect()->route('koor.dashboard');
        } elseif ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('mahasiswa.dashboard');
    }
    return redirect()->route('login');
})->name('dashboard');

/**
 * Authentication Routes
 */
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.post');

Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.post');

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

/**
 * ============================================================================
 * MAHASISWA ROUTES
 * ============================================================================
 */

Route::prefix('mahasiswa')->middleware(['auth', 'role:mahasiswa'])->group(function () {
    // Dashboard
    Route::get('/', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');

    // Pengajuan KP
    Route::prefix('pengajuan')->group(function () {
        Route::get('/', [MahasiswaController::class, 'pengajuan'])->name('mahasiswa.pengajuan');
        Route::post('/', [MahasiswaController::class, 'storePengajuan'])->name('mahasiswa.pengajuan.store');
        Route::get('/{id}/edit', [MahasiswaController::class, 'editPengajuan'])->name('mahasiswa.pengajuan.edit');
        Route::patch('/{id}', [MahasiswaController::class, 'updatePengajuan'])->name('mahasiswa.pengajuan.update');
        Route::delete('/{id}', [MahasiswaController::class, 'destroyPengajuan'])->name('mahasiswa.pengajuan.destroy');
        Route::patch('/{id}/status', [MahasiswaController::class, 'updateStatusPengajuan'])->name('mahasiswa.pengajuan.updateStatus');
    });

    // Penerimaan KP
    Route::prefix('penerimaan')->group(function () {
        Route::get('/', [MahasiswaController::class, 'penerimaan'])->name('mahasiswa.penerimaan');
        Route::get('/create', [MahasiswaController::class, 'createPenerimaan'])->name('mahasiswa.penerimaan.create');
        Route::post('/', [MahasiswaController::class, 'storePenerimaan'])->name('mahasiswa.penerimaan.store');
        Route::get('/{id}/edit', [MahasiswaController::class, 'editPenerimaan'])->name('mahasiswa.penerimaan.edit');
        Route::patch('/{id}', [MahasiswaController::class, 'updatePenerimaan'])->name('mahasiswa.penerimaan.update');
        Route::delete('/{id}', [MahasiswaController::class, 'destroyPenerimaan'])->name('mahasiswa.penerimaan.destroy');
    });

    // Data Pembimbing
    Route::prefix('pembimbing')->group(function () {
        Route::get('/', [MahasiswaController::class, 'pembimbing'])->name('mahasiswa.pembimbing');
        Route::post('/', [MahasiswaController::class, 'storePembimbing'])->name('mahasiswa.pembimbing.store');
    });

    // Seminar KP
    Route::prefix('seminar')->group(function () {
        Route::get('/', [MahasiswaController::class, 'seminar'])->name('mahasiswa.seminar');
        Route::post('/', [MahasiswaController::class, 'storeSeminar'])->name('mahasiswa.seminar.store');
    });

    // Laporan KP
    Route::prefix('laporan')->group(function () {
        Route::get('/', [MahasiswaController::class, 'laporan'])->name('mahasiswa.laporan');
        Route::post('/', [MahasiswaController::class, 'storeLaporan'])->name('mahasiswa.laporan.store');
    });

    // Nilai KP
    Route::get('/nilai', fn() => view('pages.mahasiswa.nilai'))->name('mahasiswa.nilai');
});

/**
 * Shortcut route untuk pengajuan
 */
Route::get('/pengajuan', fn() => redirect()->route('mahasiswa.pengajuan'))->name('pengajuan.index');

/**
 * ============================================================================
 * DOSEN ROUTES
 * ============================================================================
 */

Route::prefix('dosen')->name('dosen.')->middleware(['auth', 'role:dosen'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
    
    // Daftar mahasiswa bimbingan
    Route::get('/bimbingan', [DosenController::class, 'bimbingan'])->name('bimbingan');
    
    // Detail mahasiswa bimbingan
    Route::get('/bimbingan/{id}', [DosenController::class, 'show'])->name('mahasiswa.detail');
    
    // Laporan KP mahasiswa bimbingan
    Route::get('/laporan', [DosenController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/{id}/view', [DosenController::class, 'viewLaporan'])->name('laporan.view');
    Route::get('/laporan/{id}/download', [DosenController::class, 'downloadLaporan'])->name('laporan.download');
    Route::patch('/laporan/{id}/status', [DosenController::class, 'updateStatusLaporan'])->name('laporan.updateStatus');
    Route::patch('/laporan/{id}/nilai', [DosenController::class, 'updateNilaiLaporan'])->name('laporan.updateNilai');
    Route::delete('/laporan/{id}', [DosenController::class, 'destroyLaporan'])->name('laporan.destroy');
});

/**
 * ============================================================================
 * KOORDINATOR ROUTES
 * ============================================================================
 */

Route::prefix('koor')->name('koor.')->middleware(['auth', 'role:koor'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [KoordinatorController::class, 'dashboard'])->name('dashboard');
    // Pengajuan KP
    Route::prefix('pengajuan')->group(function () {
        Route::get('/', [KoordinatorController::class, 'pengajuan'])->name('pengajuan');
        Route::get('/{id}', [KoordinatorController::class, 'showPengajuan'])->name('pengajuan.show');
        Route::delete('/{id}', [KoordinatorController::class, 'destroyPengajuan'])->name('pengajuan.destroy');
    });

    // Penerimaan KP
    Route::prefix('penerimaan')->group(function () {
        Route::get('/', [KoordinatorController::class, 'penerimaan'])->name('penerimaan');
        Route::patch('/{id}/status', [KoordinatorController::class, 'updateStatusPenerimaan'])->name('penerimaan.updateStatus');
        Route::delete('/{id}', [KoordinatorController::class, 'destroyPenerimaan'])->name('penerimaan.destroy');
    });

    // Seminar KP
    Route::prefix('seminar')->group(function () {
        Route::get('/', [KoordinatorController::class, 'seminar'])->name('seminar');
        Route::patch('/{id}/status', [KoordinatorController::class, 'updateStatusSeminar'])->name('seminar.updateStatus');
        Route::delete('/{id}', [KoordinatorController::class, 'destroySeminar'])->name('seminar.destroy');
    });

    // Data Mahasiswa & Pembimbing
    Route::prefix('data-mahasiswa')->group(function () {
        Route::get('/', [KoordinatorController::class, 'dataMahasiswa'])->name('data.mahasiswa');
        Route::patch('/assign/{id}', [KoordinatorController::class, 'assignPembimbing'])->name('assign.pembimbing');
    });

    // Rekap Nilai
    Route::get('/rekap', [KoordinatorController::class, 'rekap'])->name('rekap');
});

/**
 * ============================================================================
 * ADMIN / PRODI ROUTES
 * ============================================================================
 */

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/data-mahasiswa', [AdminController::class, 'dataMahasiswa'])->name('data-mahasiswa');
    Route::get('/data-mahasiswa/export', [AdminController::class, 'exportDataMahasiswa'])->name('data-mahasiswa.export');
    Route::get('/data-perusahaan/export', [AdminController::class, 'exportDataPerusahaan'])->name('data-perusahaan.export');
    Route::get('/data-staff', [AdminController::class, 'dataStaff'])->name('data-staff');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{id}/role', [AdminController::class, 'updateRole'])->name('users.updateRole');
    Route::patch('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    
    // Perusahaan Master
    Route::resource('perusahaan', PerusahaanController::class);
});

/**
 * ============================================================================
 * PROTECTED ROUTES (with auth middleware)
 * ============================================================================
 */

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [\App\Http\Controllers\ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');
    Route::delete('/profile/photo', [\App\Http\Controllers\ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
    Route::get('/change-password', [\App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::patch('/change-password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

