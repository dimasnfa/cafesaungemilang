<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MejaController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\DetailPesananController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\CartDineinController; 
use App\Http\Controllers\Admin\CartTakeawayController; 
use App\Http\Controllers\Admin\CheckoutDineinController; 
use App\Http\Controllers\Admin\CheckoutTakeawayController; 
use App\Http\Controllers\Admin\PembayaranController; 
use App\Http\Controllers\Admin\RekomendasiMenuController; 
use App\Http\Controllers\LandingController;
use App\Http\Controllers\TakeawaySuccessController;
// use App\Http\Controllers\Admin\MidtransWebhookController;


// // ===================
// // MIDTRANS WEBHOOK
// // ===================
// Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handleWebhook']);

// =============================
// ROUTE LANDING PAGE
// =============================
Route::view('/', 'landing.index')->name('home');

// =============================
// QR CODE SCAN (SCAN DARI MEJA)
// =============================
Route::prefix('scan-qr')->group(function () {
    Route::get('/scan-qr', [LandingController::class, 'scanQr'])->name('scan.qr');
    Route::post('/process', [PesananController::class, 'processQR'])->name('scan.qr.process');
    Route::get('/{meja_id}', [CartDineinController::class, 'scanQR'])->name('scan.qr.meja');
});

// =============================
// BOOKING (DINE-IN / TAKEAWAY)
// =============================
Route::get('/{jenis}/booking', [PesananController::class, 'showBookingPage'])
    ->where('jenis', 'takeaway|dinein')
    ->name('booking');

// Tampilkan form isi data customer
Route::get('/takeaway/customer', [PesananController::class, 'showCustomerForm'])->name('takeaway.customer.form');
// Proses penyimpanan data customer dari form
Route::post('/takeaway/customer/save', [PesananController::class, 'saveCustomerData'])->name('takeaway.customer.save');

// =============================
// 📌 Route hasil scan QR ➜ TIDAK pakai middleware
// =============================
// Route::get('/dinein/booking/{meja}', [LandingController::class, 'booking'])->name('cart.dinein.booking.by.meja');
// Route::get('/dinein/booking/{meja_id}', [LandingController::class, 'booking'])->name('dinein.booking');
    Route::get('/dinein/booking/{meja_id}', [LandingController::class, 'booking'])->name('cart.dinein.booking.by.meja');

    // Route AJAX: Hapus session notif_meja setelah alert ditampilkan
    Route::post('/hapus-notif-meja', function () {
        session()->forget('notif_meja');
        return response()->json(['success' => true]);
    })->name('hapus.notif.meja');

Route::get('/takeaway/success', [TakeawaySuccessController::class, 'index'])->name('takeaway.success');

    // =============================
    // 📌 DINE-IN ROUTES ➜ WAJIB pakai middleware dinein.session
    // =============================
    Route::middleware('dinein.session')->group(function () {
    // Tidak pakai prefix 'dinein', tapi masih di dalam middleware

    Route::get('/dinein', [CartDineinController::class, 'index'])->name('cart.dinein.index');
    Route::post('/dinein/store', [CartDineinController::class, 'store'])->name('cart.dinein.store');
    Route::post('/dinein/update', [CartDineinController::class, 'update'])->name('cart.dinein.update');
    Route::delete('/dinein/destroy/{id}', [CartDineinController::class, 'destroy'])->name('cart.dinein.destroy');
    Route::post('/dinein/clear', [CartDineinController::class, 'clearCart'])->name('cart.dinein.clear');
    Route::get('/dinein/cart', [CartDineinController::class, 'dineinCart'])->name('cart.dinein.cart');

    // Routes checkout tanpa prefix grup, tapi URL tetap lengkap
    Route::post('/dinein/checkout/process', [CheckoutDineinController::class, 'process'])->name('cart.dinein.checkout.process');
    Route::post('/dinein/checkout/callback', [CheckoutDineinController::class, 'dineInCallback'])->name('cart.dinein.checkout.callback');
    Route::get('/dinein/checkout/success', [CheckoutDineinController::class, 'dineInSuccess'])->name('cart.dinein.checkout.success');

    // Route payment Cash
     Route::post('/dinein/checkout/cash', [CheckoutDineinController::class, 'processCash'])->name('cart.dinein.checkout.cash');
     Route::get('/dinein/checkout/sukses', [CheckoutDineinController::class, 'cashSuccess'])->name('cart.dinein.checkout.sukses');

    });


// =============================
// TAKEAWAY ROUTES (with middleware)
// =============================
Route::middleware('takeaway.session')->group(function () {

    Route::get('/takeaway/booking', [LandingController::class, 'takeaway'])->name('takeaway.booking');

    Route::get('/takeaway', [CartTakeawayController::class, 'index'])->name('cart.takeaway.index');
    Route::post('/takeaway/store', [CartTakeawayController::class, 'store'])->name('cart.takeaway.store');
    Route::post('/takeaway/update', [CartTakeawayController::class, 'update'])->name('cart.takeaway.update');
    Route::delete('/takeaway/destroy/{id}', [CartTakeawayController::class, 'destroy'])->name('cart.takeaway.destroy');
    Route::post('/takeaway/clear', [CartTakeawayController::class, 'clearCart'])->name('cart.takeaway.clear');
    Route::get('/takeaway/cart', [CartTakeawayController::class, 'takeawayCart'])->name('cart.takeaway.cart');

    // Checkout Takeaway (tanpa prefix juga, tetap dalam URL lengkap)
    Route::post('/takeaway/checkout/process', [CheckoutTakeawayController::class, 'process'])->name('cart.takeaway.checkout.process');
    Route::post('/takeaway/checkout/callback', [CheckoutTakeawayController::class, 'takeawayCallback'])->name('cart.takeaway.checkout.callback');
    Route::get('/takeaway/checkout/success', [CheckoutTakeawayController::class, 'takeawaySuccess'])->name('cart.takeaway.checkout.success');

});



// Rekomendasi untuk dine-in (dengan meja_id)
// Rekomendasi dine-in tanpa meja_id di URL
Route::get('/dinein/rekomendasi', [RekomendasiMenuController::class, 'showRekomendasi'])->name('rekomendasi.dinein');

// Rekomendasi umum (tidak pakai meja_id)
Route::get('/menu/rekomendasi/{tipe}', [RekomendasiMenuController::class, 'indexPublic'])->name('menu.rekomendasi');


// =============================
// SIMPAN DATA PELANGGAN TAKEAWAY
// =============================
// Route::post('/takeaway/simpan-identitas', [PesananController::class, 'simpanIdentitas'])->name('takeaway.customer.save');

// =============================
// ROUTE PESANAN
// =============================
Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
Route::get('/pesanan/{id}', [PesananController::class, 'show'])->name('pesanan.show');
Route::post('/pesanan/store', [PesananController::class, 'store'])->name('pesanan.store');
Route::post('/pesanan/update/{id}', [PesananController::class, 'update'])->name('pesanan.update');
Route::delete('/pesanan/delete/{id}', [PesananController::class, 'destroy'])->name('pesanan.destroy');

// // =============================
// // ROUTE CHECKOUT (Umum)
// // =============================
// Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
// Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout');
// Route::post('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
// Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

// =============================
// ROUTE LOGIN, REGISTER, LOGOUT
// =============================
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login-proses', [LoginController::class, 'login_proses'])->name('login_proses');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [LoginController::class, 'register'])->name('register');
Route::post('/register-proses', [LoginController::class, 'register_proses'])->name('register_proses');

// =============================
// ROUTE RESET PASSWORD
// =============================
// Reset Password
Route::get('/forgot-password', [LoginController::class, 'forgot_password'])->name('forgot-password');
Route::post('/forgot-password', [LoginController::class, 'forgot_password_act'])->name('forgot-password-act');
Route::get('/reset-password/{token}', [LoginController::class, 'validasi_forgot_password'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'validasi_forgot_password_act'])->name('reset-password-act');
// =============================
// ROUTE PROFILE USER
// =============================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [LoginController::class, 'profile'])->name('profile');
    Route::post('/ubah-sandi', [LoginController::class, 'ubah_sandi'])->name('ubah-sandi');
});

// =============================
// ROUTE ADMIN & KASIR
// =============================
Route::prefix('admin')->middleware(['auth', 'role:admin|kasir'])->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.pages.dashboard.index'))->name('dashboard');

    // Kategori, Menu, Meja
    Route::resource('/kategori', KategoriController::class)->except(['show']);
    Route::resource('/menu', MenuController::class)->except(['show']);
    Route::resource('/meja', MejaController::class)->except(['show']);

    // User (Hanya Admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('/user', UserAdminController::class)->except(['show']);
    });

    // Pesanan
    Route::resource('/pesanan', PesananController::class);

    // Detail Pesanan
    Route::prefix('detailpesanan')->name('detailpesanan.')->group(function () {
        Route::get('/', [DetailPesananController::class, 'index'])->name('index');
        Route::get('/create', [DetailPesananController::class, 'create'])->name('create');
        Route::post('/store', [DetailPesananController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [DetailPesananController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DetailPesananController::class, 'update'])->name('update');
        Route::delete('/{id}/destroy', [DetailPesananController::class, 'destroy'])->name('destroy');
    });
    

    // =============================
    // ROUTE PEMBAYARAN (ADMIN / KASIR)
    // =============================
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/create', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('/pembayaran/{id}/edit', [PembayaranController::class, 'edit'])->name('pembayaran.edit');
    Route::put('/pembayaran/{id}', [PembayaranController::class, 'update'])->name('pembayaran.update');
    Route::delete('/pembayaran/{id}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
    Route::get('/pembayaran/{id}/invoice', [PembayaranController::class, 'invoice'])->name('pembayaran.invoice');

    // routes/web.php
    Route::get('/admin/pembayaran/{id}/invoice', [PembayaranController::class, 'invoice'])->name('admin.pembayaran.invoice');

    
    // Laporan
   Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // Route export PDF laporan
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');

    // Route export CSV laporan (jika ada)
    Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export-csv');

    // Route lainnya untuk laporan...
    Route::post('/laporan/filter', [LaporanController::class, 'filter'])->name('laporan.filter');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
});
