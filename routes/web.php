<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminAuth;

// ==========================================
//          RUTE UNTUK PELANGGAN (PUBLIC)
// ==========================================
Route::get('/', [BookingController::class, 'index'])->name('booking.index');
Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/pembayaran/{id}', [BookingController::class, 'pembayaran'])->name('booking.pembayaran');
Route::post('/booking/pembayaran/{id}/upload', [BookingController::class, 'uploadBukti'])->name('booking.upload');
Route::get('/booking/check-status/{id}', [BookingController::class, 'checkStatus'])->name('booking.check');
Route::get('/booking/check-status/{id}', [BookingController::class, 'checkStatus'])->name('booking.check_status');

// ==========================================
//          RUTE AUTENTIKASI ADMIN
// ==========================================
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
//    RUTE PROTECTED DASHBOARD ADMIN
//    (Hanya bisa diakses jika sudah login)
// ==========================================
Route::middleware([AdminAuth::class])->group(function () {

    // Dashboard & Pengaturan Utama
    Route::get('/admin/dashboard', [BookingController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/booking/table-refresh', [BookingController::class, 'getTablePartial'])->name('admin.table.refresh');
    Route::post('/admin/booking/reset', [BookingController::class, 'resetAntrian'])->name('admin.reset_antrian');
    Route::post('/admin/booking/manual', [BookingController::class, 'storeManual'])->name('admin.booking.manual');

    Route::get('/admin/ganti-sandi', [AuthController::class, 'showChangePassword'])->name('admin.ganti_sandi');
    Route::post('/admin/ganti-sandi', [AuthController::class, 'updatePassword'])->name('admin.update_password');

    // Rute Alur Antrian
    Route::post('/admin/booking/{id}/panggil', [BookingController::class, 'panggilPelanggan'])->name('admin.panggil');
    Route::post('/admin/booking/{id}/panggil-kembali', [BookingController::class, 'panggilKembali'])->name('admin.panggil_kembali');
    Route::post('/admin/booking/{id}/mulai', [BookingController::class, 'adminMulaiBermain'])->name('admin.mulai');
    
    // Rute Baru untuk Input Meja via Modal
    Route::post('/admin/mulai-proses', [BookingController::class, 'mulaiProses'])->name('admin.mulai_proses');
    
    Route::post('/admin/booking/{id}/selesai', [BookingController::class, 'adminSelesai'])->name('admin.selesai');
    Route::post('/admin/booking/{id}/lewati', [BookingController::class, 'lewatiAntrian'])->name('admin.lewati');
    
    // Rute Pembayaran & Durasi
    Route::post('/admin/verifikasi/{id}', [BookingController::class, 'verifikasiPembayaran'])->name('admin.verifikasi');
    Route::post('/admin/tolak/{id}', [BookingController::class, 'tolakPembayaran'])->name('admin.tolak');
    Route::post('/admin/booking/tambah-durasi/{id}', [BookingController::class, 'tambahDurasi'])->name('admin.booking.tambah_durasi');

});