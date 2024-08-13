<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;

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
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Register routes -------------------------------------------------------------
Route::get('/register', [RegisterController::class, 'index'])->name('register');

Route::middleware(['auth'])->group(function () {
    Route::get('/kepsek/dashboard', [DashboardController::class, 'kepsek'])->name('kepsek.dashboard');
    Route::get('/bendahara/dashboard', [DashboardController::class, 'bendahara'])->name('bendahara.dashboard');
    Route::get('/walikelas/dashboard', [DashboardController::class, 'walikelas'])->name('walikelas.dashboard');
    Route::get('/siswa/dashboard', [DashboardController::class, 'siswa'])->name('siswa.dashboard');
});
