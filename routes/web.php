<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Dapur\DashboardController as DapurDashboard;
use App\Http\Controllers\Dapur\OrderStatusController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboard;
use App\Http\Controllers\Kasir\InvoiceController;
use App\Http\Controllers\Kasir\PaymentController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderMonitorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

// Fallback dashboard (redirect ke role masing-masing)
Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'kasir' => redirect()->route('kasir.dashboard'),
        'dapur' => redirect()->route('dapur.dashboard'),
        default => view('dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Lihat Pesanan (monitoring) — filter status & rentang tanggal
    Route::get('/orders', [OrderMonitorController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderMonitorController::class, 'show'])->name('orders.show');

    // Manajemen Pengguna
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])
        ->name('users.toggle-active');

    // Manajemen Kategori Makanan/Minuman
    Route::resource('categories', CategoryController::class)
        ->except(['show', 'create', 'edit']);

    // Manajemen Data Produk/Menu
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-availability', [ProductController::class, 'toggleAvailability'])
        ->name('products.toggle-availability');
    Route::get('products-popular', [ProductController::class, 'popular'])
        ->name('products.popular');

    // Kumpulan Review Makanan
    Route::resource('reviews', ReviewController::class)
        ->only(['index', 'destroy']);

    // Pengaturan Nomor Meja & Generate QR Code
    Route::resource('tables', TableController::class)
        ->only(['index', 'store', 'destroy']);
    Route::post('tables/{table}/regenerate-qr', [TableController::class, 'regenerateQr'])
        ->name('tables.regenerate-qr');
    Route::get('tables/{table}/print-qr', [TableController::class, 'printQr'])
        ->name('tables.print-qr');

    // Rute-rute Untuk Fitur Laporan (Admin)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/top-products', [ReportController::class, 'topProducts'])->name('top-products');
        Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/pdf/daily', [ReportController::class, 'exportDailyPdf'])->name('export.pdf.daily');
        Route::get('/export/pdf/monthly', [ReportController::class, 'exportMonthlyPdf'])->name('export.pdf.monthly');
        Route::get('/export/pdf/top-products', [ReportController::class, 'exportTopProductsPdf'])->name('export.pdf.top-products');
        Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    });
});

// Routing Halaman Pelanggan (scan QR tanpa login)
Route::prefix('order')->name('order.')->group(function () {
    // Simulasi pelanggan (demo): pilih meja acak yang tersedia lalu buka menunya
    Route::get('/simulasi', [OrderController::class, 'simulasi'])->name('simulasi');
    Route::get('/menu/{table}', [OrderController::class, 'menu'])->name('menu');
    Route::post('/menu/{table}', [OrderController::class, 'store'])->name('store');
    Route::get('/confirm/{order}', [OrderController::class, 'confirm'])->name('confirm');
    Route::get('/thanks/{order}', [OrderController::class, 'thanks'])->name('thanks');
    Route::get('/status/{order}', [OrderController::class, 'status'])->name('status');
    Route::post('/complete/{order}', [OrderController::class, 'complete'])->name('complete');
    Route::post('/review', [OrderController::class, 'review'])->name('review');
});

// Kasir routes
Route::middleware(['auth', 'verified', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirDashboard::class, 'index'])->name('dashboard');
    Route::get('/poll', [KasirDashboard::class, 'poll'])->name('poll');

    // Lihat Pesanan (monitoring) — filter status & rentang tanggal
    Route::get('/orders', [OrderMonitorController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderMonitorController::class, 'show'])->name('orders.show');

    // Sistem Konfirmasi Pembayaran Kasir
    Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{order}/cash', [PaymentController::class, 'confirmCash'])->name('payment.cash');

    // Halaman Cetak Nota/Invoice
    Route::get('/invoice/{order}', [InvoiceController::class, 'show'])->name('invoice');
});

// Integrasi Pembayaran Online Midtrans
Route::prefix('midtrans')->name('midtrans.')->group(function () {
    Route::get('/snap-token/{order}', [MidtransController::class, 'snapToken'])->name('snap-token');
    Route::post('/webhook', [MidtransController::class, 'webhook'])->name('webhook')->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/verify/{order}', [MidtransController::class, 'verify'])->name('verify');
});

// Dapur routes
Route::middleware(['auth', 'verified', 'role:dapur'])->prefix('dapur')->name('dapur.')->group(function () {
    Route::get('/dashboard', [DapurDashboard::class, 'index'])->name('dashboard');
    Route::get('/poll', [DapurDashboard::class, 'poll'])->name('poll');

    // Fitur buat staf dapur nge-update status makanan
    Route::patch('/orders/{order}/status', [OrderStatusController::class, 'update'])
        ->name('orders.status');
});

// Profile (semua role)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
