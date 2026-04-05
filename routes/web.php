<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboard;
use App\Http\Controllers\Dapur\DashboardController as DapurDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);

// Fallback dashboard (redirect ke role masing-masing)
Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    return match ($role) {
        'admin'  => redirect()->route('admin.dashboard'),
        'kasir'  => redirect()->route('kasir.dashboard'),
        'dapur'  => redirect()->route('dapur.dashboard'),
        default  => view('dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Manajemen Pengguna
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::patch('users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])
        ->name('users.toggle-active');

    // Manajemen Kategori Makanan/Minuman
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)
        ->except(['show', 'create', 'edit']);

    // Manajemen Data Produk/Menu
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::patch('products/{product}/toggle-availability', [\App\Http\Controllers\Admin\ProductController::class, 'toggleAvailability'])
        ->name('products.toggle-availability');
    Route::get('products-popular', [\App\Http\Controllers\Admin\ProductController::class, 'popular'])
        ->name('products.popular');

    // Kumpulan Review Makanan
    Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class)
        ->only(['index', 'destroy']);

    // Pengaturan Nomor Meja & Generate QR Code
    Route::resource('tables', \App\Http\Controllers\Admin\TableController::class)
        ->only(['index', 'store', 'destroy']);
    Route::post('tables/{table}/regenerate-qr', [\App\Http\Controllers\Admin\TableController::class, 'regenerateQr'])
        ->name('tables.regenerate-qr');
    Route::get('tables/{table}/print-qr', [\App\Http\Controllers\Admin\TableController::class, 'printQr'])
        ->name('tables.print-qr');

    // Rute-rute Untuk Fitur Laporan (Admin)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily',        [\App\Http\Controllers\Admin\ReportController::class, 'daily'])->name('daily');
        Route::get('/monthly',      [\App\Http\Controllers\Admin\ReportController::class, 'monthly'])->name('monthly');
        Route::get('/top-products', [\App\Http\Controllers\Admin\ReportController::class, 'topProducts'])->name('top-products');
        Route::get('/export/pdf',   [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('export.excel');
    });
});

// Routing Halaman Pelanggan (scan QR tanpa login)
Route::prefix('order')->name('order.')->group(function () {
    Route::get('/menu/{table}',    [\App\Http\Controllers\OrderController::class, 'menu'])->name('menu');
    Route::post('/menu/{table}',   [\App\Http\Controllers\OrderController::class, 'store'])->name('store');
    Route::get('/confirm/{order}', [\App\Http\Controllers\OrderController::class, 'confirm'])->name('confirm');
    Route::get('/status/{order}',  [\App\Http\Controllers\OrderController::class, 'status'])->name('status');
    Route::post('/review',         [\App\Http\Controllers\OrderController::class, 'review'])->name('review');
});

// Kasir routes
Route::middleware(['auth', 'verified', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirDashboard::class, 'index'])->name('dashboard');
    Route::get('/poll',      [KasirDashboard::class, 'poll'])->name('poll');

    // Sistem Konfirmasi Pembayaran Kasir
    Route::get('/payment/{order}',       [\App\Http\Controllers\Kasir\PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{order}/cash', [\App\Http\Controllers\Kasir\PaymentController::class, 'confirmCash'])->name('payment.cash');

    // Halaman Cetak Nota/Invoice
    Route::get('/invoice/{order}', [\App\Http\Controllers\Kasir\InvoiceController::class, 'show'])->name('invoice');
});

// Integrasi Pembayaran Online Midtrans
Route::prefix('midtrans')->name('midtrans.')->group(function () {
    Route::get('/snap-token/{order}', [\App\Http\Controllers\MidtransController::class, 'snapToken'])->name('snap-token');
    Route::post('/webhook',           [\App\Http\Controllers\MidtransController::class, 'webhook'])->name('webhook')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/verify/{order}',    [\App\Http\Controllers\MidtransController::class, 'verify'])->name('verify');
});

// Dapur routes
Route::middleware(['auth', 'verified', 'role:dapur'])->prefix('dapur')->name('dapur.')->group(function () {
    Route::get('/dashboard', [DapurDashboard::class, 'index'])->name('dashboard');
    Route::get('/poll',      [DapurDashboard::class, 'poll'])->name('poll');

    // Fitur buat staf dapur nge-update status makanan
    Route::patch('/orders/{order}/status', [\App\Http\Controllers\Dapur\OrderStatusController::class, 'update'])
        ->name('orders.status');
});

// Profile (semua role)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
