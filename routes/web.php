<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

// Redirect halaman root ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Halaman Utama Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Pemesanan Kendaraan
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
    Route::get('/bookings/export', [BookingController::class, 'export'])->name('bookings.export');

    // Tindakan Persetujuan Berjenjang (Approver)
    Route::post('/approvals/{approval}/approve', [BookingController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{approval}/reject', [BookingController::class, 'reject'])->name('approvals.reject');

    // Monitoring Kesiapan dan Riwayat Armada
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');

    // Pengelolaan Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
