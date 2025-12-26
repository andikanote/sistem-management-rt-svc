<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon; // <--- Penting: Tambahkan ini untuk fitur tanggal dinamis

// 1. Halaman Awal langsung lempar ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Dashboard (Hanya bisa diakses kalau sudah login)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 3. Grouping Route untuk Fitur RT (Butuh Login)
Route::middleware('auth')->group(function () {
    // Route bawaan Breeze untuk edit profil user
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route Pembayaran
    Route::get('/payment/{invoice}', [PaymentController::class, 'pay'])->name('payment.pay');

    // === ROUTE ADMIN: MANUAL TRIGGER SCHEDULER ===
    Route::post('/admin/generate-invoices', function () {
        // 1. Panggil command artisan via kode
        Artisan::call('invoice:generate-monthly');

        // 2. Ambil format Bulan Tahun saat ini (Contoh: Desember 2025)
        // locale('id') memastikan nama bulan dalam Bahasa Indonesia
        $periode = Carbon::now()->locale('id')->isoFormat('MMMM Y');

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses dinamis
        return back()->with('success', "Scheduler dijalankan: Tagihan bulan $periode berhasil di Generate!");
    })->name('admin.invoices.generate');

    // === ROUTE ADMIN: DELETE INVOICE ===
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    // === ROUTE ADMIN: BAYAR MANUAL ===
    Route::patch('/invoices/{id}/paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.markPaid');
});

// 4. Webhook Midtrans/Xendit (Jangan dimasukkan ke dalam middleware auth!)
Route::post('/xendit/callback', [PaymentController::class, 'callback']);

require __DIR__.'/auth.php';
