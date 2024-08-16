<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WalikelasController;
use App\Http\Controllers\SiswaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Login routes ----------------------------------------------------------------
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Logout routes ---------------------------------------------------------------
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Register routes -------------------------------------------------------------
Route::get('/register', [RegisterController::class, 'index'])->name('register');

Route::middleware(['auth'])->group(function () {

    // Kepsek --------------------------------------------------------------------------------------------------------------------
    Route::get('/kepsek/dashboard', [DashboardController::class, 'kepsek'])->name('kepsek.dashboard');

    // Bendahara -----------------------------------------------------------------------------------------------------------------
    Route::get('/bendahara/dashboard', [DashboardController::class, 'bendahara'])->name('bendahara.dashboard');

        // Bendahara Kelola Walikelas --------------------------------------------------------------------------------------------
        Route::get('/bendahara/kelola-walikelas', [WalikelasController::class, 'index'])->name('walikelas.index');
        Route::get('/bendahara/kelola-walikelas', [WalikelasController::class, 'index'])->name('walikelas.index');
        Route::post('/bendahara/kelola-walikelas/tambah', [WalikelasController::class, 'add'])->name('walikelas.add');
        Route::get('/bendahara/kelola-walikelas/get-data/{id}', [WalikelasController::class, 'getWalikelasData'])->name('walikelas.getData');
        Route::put('/bendahara/kelola-walikelas/edit/{id}', [WalikelasController::class, 'edit'])->name('walikelas.edit');
        Route::delete('/bendahara/kelola-walikelas/hapus/{id}', [WalikelasController::class, 'delete'])->name('walikelas.delete');

        // Bendahara Kelola Siswa ------------------------------------------------------------------------------------------------
        Route::get('/bendahara/kelola-siswa', [SiswaController::class, 'index'])->name('siswa.index');
        Route::get('/bendahara/kelola-siswa', [SiswaController::class, 'index'])->name('siswa.index');
        Route::post('/bendahara/kelola-siswa/tambah', [SiswaController::class, 'add'])->name('siswa.add');
        Route::get('/bendahara/kelola-siswa/get-data/{id}', [SiswaController::class, 'getSiswaData'])->name('siswa.getData');
        Route::put('/bendahara/kelola-siswa/edit/{id}', [SiswaController::class, 'edit'])->name('siswa.edit');
        Route::delete('/bendahara/kelola-siswa/hapus/{id}', [SiswaController::class, 'delete'])->name('siswa.delete');

    // Walikelas -----------------------------------------------------------------------------------------------------------------
    Route::get('/walikelas/dashboard', [DashboardController::class, 'walikelas'])->name('walikelas.dashboard');

    // Siswa ---------------------------------------------------------------------------------------------------------------------
    Route::get('/siswa/dashboard', [DashboardController::class, 'siswa'])->name('siswa.dashboard');

});
