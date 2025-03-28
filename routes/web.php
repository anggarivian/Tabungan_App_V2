<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TabunganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\WalikelasController;

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

// Menampilkan halaman welcome sebagai halaman utama aplikasi.
Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return redirect('/');
});

// Menangani autentikasi dan otorisasi pengguna.
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/change-password', [LoginController::class, 'change_password'])->name('change.password')->middleware('auth');
Route::post('/change-password', [LoginController::class, 'change_password_submit'])->name('change.password.submit')->middleware('auth');

// Menangani proses registrasi pengguna baru.
Route::get('/register', [RegisterController::class, 'index'])->name('register');

// Grup middleware untuk pengguna yang telah terautentikasi.
Route::middleware(['auth'])->group(function () {
    // Grup middleware untuk pengguna dengan role Kepsek.
    Route::middleware('isKepsek')->group(function () {
        // Menampilkan dashboard untuk Kepsek.
        Route::get('/kepsek/dashboard', [DashboardController::class, 'kepsek'])->name('kepsek.dashboard');

        // Menangani pengelolaan Laporan.
        Route::get('/kepsek/laporan/tabungan', [LaporanController::class, 'lap_kepsek_tabungan'])->name('laporan.kepsek.tabungan');
        Route::get('/kepsek/laporan/transaksi', [LaporanController::class, 'lap_kepsek_transaksi'])->name('laporan.kepsek.transaksi');
        Route::get('/kepsek/laporan/pengajuan', [LaporanController::class, 'lap_kepsek_pengajuan'])->name('laporan.kepsek.pengajuan');
        Route::get('/kepsek/laporan/export', [LaporanController::class, 'lap_kepsek_export'])->name('laporan.kepsek.export');
            Route::post('/kepsek/laporan/export/tabungan', [ExportController::class, 'kepsek_exportTabungan'])->name('kepsek.export.tabungan');
            Route::post('/kepsek/laporan/export/transaksi', [ExportController::class, 'kepsek_exportTransaksi'])->name('kepsek.export.transaksi');
            Route::post('/kepsek/laporan/export/pengajuan', [ExportController::class, 'kepsek_exportPengajuan'])->name('kepsek.export.pengajuan');
    });

    // Grup middleware untuk pengguna dengan role Bendahara.
    Route::middleware('isBendahara')->group(function () {
        // Menampilkan dashboard untuk Bendahara.
        Route::get('/bendahara/dashboard', [DashboardController::class, 'bendahara'])->name('bendahara.dashboard');

        // Menangani pengelolaan Walikelas.
        Route::get('/bendahara/kelola-walikelas', [WalikelasController::class, 'index'])->name('bendahara.walikelas.index');
        Route::post('/bendahara/kelola-walikelas/tambah', [WalikelasController::class, 'add'])->name('bendahara.walikelas.add');
        Route::get('/bendahara/kelola-walikelas/get-data/{id}', [WalikelasController::class, 'getWalikelasData'])->name('bendahara.walikelas.getData');
        Route::put('/bendahara/kelola-walikelas/edit/{id}', [WalikelasController::class, 'edit'])->name('bendahara.walikelas.edit');
        Route::delete('/bendahara/kelola-walikelas/hapus/{id}', [WalikelasController::class, 'delete'])->name('bendahara.walikelas.delete');

        // Menangani pengelolaan Siswa.
        Route::get('/bendahara/kelola-siswa', [SiswaController::class, 'index'])->name('bendahara.siswa.index');
        Route::post('/bendahara/kelola-siswa/tambah', [SiswaController::class, 'add'])->name('bendahara.siswa.add');
        Route::get('/bendahara/kelola-siswa/get-data/{id}', [SiswaController::class, 'getSiswaData'])->name('bendahara.siswa.getData');
        Route::put('/bendahara/kelola-siswa/edit/{id}', [SiswaController::class, 'edit'])->name('bendahara.siswa.edit');
        Route::delete('/bendahara/kelola-siswa/hapus/{id}', [SiswaController::class, 'delete'])->name('bendahara.siswa.delete');
        Route::post('/bendahara/kelola-siswa/import', [SiswaController::class, 'importExcel'])->name('siswa.import');

        // Menangani pengelolaan Tabungan.
        Route::get('/bendahara/tabungan', [TabunganController::class, 'bendahara_index'])->name('bendahara.tabungan.index');
        Route::get('/bendahara/tabungan/stor', [TabunganController::class, 'bendahara_stor'])->name('bendahara.tabungan.stor');
        Route::get('/bendahara/tabungan/stor/kelas/{id}', [TabunganController::class, 'bendahara_storMasal'])->name('bendahara.tabungan.stor.masal');
        Route::post('/bendahara/tabungan/stor/kelas/add', [TabunganController::class, 'bendahara_storMasalTabungan'])->name('bendahara.tabungan.storMasalTabungan');
        Route::post('/bendahara/tabungan/stor/add', [TabunganController::class, 'bendahara_storTabungan'])->name('bendahara.tabungan.storTabungan');
        Route::get('/bendahara/tabungan/tarik', [TabunganController::class, 'bendahara_tarik'])->name('bendahara.tabungan.tarik');
        Route::post('/bendahara/tabungan/tarik/add', [TabunganController::class, 'bendahara_tarikTabungan'])->name('bendahara.tabungan.tarikTabungan');
        Route::get('/bendahara/tabungan/search', [TabunganController::class, 'bendahara_search'])->name('bendahara.search');

        // Menangani pengelolaan Pengajuan.
        Route::get('/bendahara/kelola-pengajuan', [PengajuanController::class, 'kelola_pengajuan'])->name('bendahara.pengajuan.index');
        Route::get('/bendahara/kelola-pengajuan/get-data/{id}', [PengajuanController::class, 'getPengajuanData'])->name('bendahara.pengajuan.getData');
        Route::put('/bendahara/kelola-pengajuan/terima', [PengajuanController::class, 'terima'])->name('bendahara.pengajuan.terima');
        Route::get('/bendahara/kelola-pengajuan/tolak/{id}', [PengajuanController::class, 'tolak'])->name('bendahara.pengajuan.tolak');

        // Menangani pengelolaan Laporan.
        Route::get('/bendahara/laporan/tabungan', [LaporanController::class, 'lap_bendahara_tabungan'])->name('laporan.bendahara.tabungan');
        Route::get('/bendahara/laporan/transaksi', [LaporanController::class, 'lap_bendahara_transaksi'])->name('laporan.bendahara.transaksi');
        Route::get('/bendahara/laporan/pengajuan', [LaporanController::class, 'lap_bendahara_pengajuan'])->name('laporan.bendahara.pengajuan');
        Route::get('/bendahara/laporan/export', [LaporanController::class, 'lap_bendahara_export'])->name('laporan.bendahara.export');
            Route::post('/bendahara/laporan/export/tabungan', [ExportController::class, 'bendahara_exportTabungan'])->name('bendahara.export.tabungan');
            Route::post('/bendahara/laporan/export/transaksi', [ExportController::class, 'bendahara_exportTransaksi'])->name('bendahara.export.transaksi');
            Route::post('/bendahara/laporan/export/pengajuan', [ExportController::class, 'bendahara_exportPengajuan'])->name('bendahara.export.pengajuan');
    });

    // Grup middleware untuk pengguna dengan role Walikelas.
    Route::middleware('isWalikelas')->group(function () {
        // Menampilkan dashboard untuk Walikelas.
        Route::get('/walikelas/dashboard', [DashboardController::class, 'walikelas'])->name('walikelas.dashboard');

        // Menangani pengelolaan Tabungan.
        Route::get('/walikelas/tabungan', [TabunganController::class, 'walikelas_index'])->name('walikelas.tabungan.index');
        Route::get('/walikelas/tabungan/stor', [TabunganController::class, 'walikelas_stor'])->name('walikelas.tabungan.stor');
        Route::get('/walikelas/tabungan/stor/kelas', [TabunganController::class, 'walikelas_storMasal'])->name('walikelas.tabungan.stor.masal');
        Route::post('/walikelas/tabungan/stor/kelas/add', [TabunganController::class, 'walikelas_storMasalTabungan'])->name('walikelas.tabungan.storMasalTabungan');
        Route::post('/walikelas/tabungan/stor/add', [TabunganController::class, 'walikelas_storTabungan'])->name('walikelas.tabungan.storTabungan');
        Route::get('/walikelas/tabungan/search', [TabunganController::class, 'walikelas_search'])->name('walikelas.search');

        // Menangani pengelolaan Laporan.
        Route::get('/walikelas/laporan/tabungan', [LaporanController::class, 'lap_walikelas_tabungan'])->name('laporan.walikelas.tabungan');
        Route::get('/walikelas/laporan/transaksi', [LaporanController::class, 'lap_walikelas_transaksi'])->name('laporan.walikelas.transaksi');
        Route::get('/walikelas/laporan/export', [LaporanController::class, 'lap_walikelas_export'])->name('laporan.walikelas.export');
            Route::post('/walikelas/laporan/export/tabungan', [ExportController::class, 'walikelas_exportTabungan'])->name('walikelas.export.tabungan');
            Route::post('/walikelas/laporan/export/transaksi', [ExportController::class, 'walikelas_exportTransaksi'])->name('walikelas.export.transaksi');
    });

    // Grup middleware untuk pengguna dengan role Siswa.
    Route::middleware('isSiswa')->group(function () {
        // Menampilkan dashboard untuk Siswa.
        Route::get('/siswa/dashboard', [DashboardController::class, 'siswa'])->name('siswa.dashboard');

        // Menangani pengelolaan Tabungan.
        Route::get('/siswa/tabungan/stor', [TabunganController::class, 'siswa_stor'])->name('siswa.tabungan.stor');

        Route::get('/siswa/tabungan/tarik', [TabunganController::class, 'siswa_tarik'])->name('siswa.tabungan.tarik');
        Route::post('/siswa/tabungan/tarik/ajukan', [PengajuanController::class, 'ajukan'])->name('siswa.tabungan.ajukan');


        // Menangani pengelolaan Laporan.
        Route::get('/siswa/laporan/tabungan', [LaporanController::class, 'lap_siswa_tabungan'])->name('laporan.siswa.tabungan');
        Route::get('/siswa/laporan/transaksi', [LaporanController::class, 'lap_siswa_transaksi'])->name('laporan.siswa.transaksi');
        Route::get('/siswa/laporan/pengajuan', [LaporanController::class, 'lap_siswa_pengajuan'])->name('laporan.siswa.pengajuan');
        Route::get('/siswa/laporan/export', [LaporanController::class, 'lap_siswa_export'])->name('laporan.siswa.export');
            Route::post('/siswa/laporan/export/tabungan', [ExportController::class, 'siswa_exportTabungan'])->name('siswa.export.tabungan');
            Route::post('/siswa/laporan/export/transaksi', [ExportController::class, 'siswa_exportTransaksi'])->name('siswa.export.transaksi');
            Route::post('/siswa/laporan/export/pengajuan', [ExportController::class, 'siswa_exportPengajuan'])->name('siswa.export.pengajuan');
    });
});

Route::post('/siswa/tabungan/stor', [TabunganController::class, 'createInvoice'])->name('siswa.tabungan.store');
Route::get('/payout-channels', [PengajuanController::class, 'getPayoutChannels'])->name('payout.channels');
Route::post('/xendit/webhook', [TabunganController::class, 'handleWebhook']);

route::get('/offline', function () {
    return view('modules/laravelpwa/offline');
});

Route::get('/clear-cache', function() {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    \Artisan::call('optimize:clear');
    return 'Cache Cleared!';
});

