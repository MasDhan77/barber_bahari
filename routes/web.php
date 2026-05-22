<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController; // Pastikan buat controller ini nanti
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReviewController;

// 1. Halaman Publik (Tanpa Login)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog-rambut', [HomeController::class, 'allGalleries'])->name('gallery.index');

// 2. Rute Akses Universal (Asal sudah Login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 3. Rute Khusus CUSTOMER (Menggunakan Middleware Role)
Route::middleware(['auth', 'verified', 'role:customer'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/booking/cancel/{id}', [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::post('/booking/{booking_id}/review', [ReviewController::class, 'store'])->name('customer.review.store');
});

// 4. Rute Khusus ADMIN & SUPERADMIN
Route::middleware(['auth', 'role:admin,superadmin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/bookings', [AdminController::class, 'manageBookings'])->name('admin.bookings');
    
    // Tambahkan aksi admin seperti konfirmasi/selesai di sini
    Route::post('/bookings/confirm/{id}', [AdminController::class, 'confirm'])->name('admin.bookings.confirm');
    Route::post('/bookings/complete/{id}', [AdminController::class, 'complete'])->name('admin.bookings.complete');
    Route::post('/bookings/reject/{id}', [AdminController::class, 'reject'])->name('admin.bookings.reject');
    Route::post('/bookings/noshow/{id}', [AdminController::class, 'noShow'])->name('admin.bookings.noshow');
    Route::get('/reviews', [ReviewController::class, 'adminIndex'])->name('admin.reviews');
    
    Route::get('/galleries', [AdminController::class, 'galleries'])->name('admin.galleries');
    Route::post('/galleries', [AdminController::class, 'storeGallery'])->name('admin.galleries.store');
    Route::put('/galleries/{id}', [AdminController::class, 'updateGallery'])->name('admin.galleries.update');
    Route::delete('/galleries/{id}', [AdminController::class, 'deleteGallery'])->name('admin.galleries.delete');
});

// 5. Rute Eksklusif SUPERADMIN (Manajemen Data Master)
Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->group(function () {
    Route::get('/barbers', [AdminController::class, 'manageBarbers'])->name('admin.barbers');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');

    Route::post('/barbers', [AdminController::class, 'storeBarber'])->name('admin.barbers.store');
    Route::delete('/barbers/{id}', [AdminController::class, 'deleteBarber'])->name('admin.barbers.delete');
    Route::put('/barbers/{id}', [AdminController::class, 'updateBarber'])->name('admin.barbers.update');

    Route::get('/services', [AdminController::class, 'manageServices'])->name('admin.services');
    Route::post('/services', [AdminController::class, 'storeService'])->name('admin.services.store');
    Route::put('/services/{id}', [AdminController::class, 'updateService'])->name('admin.services.update');
    Route::delete('/services/{id}', [AdminController::class, 'deleteService'])->name('admin.services.delete');

    Route::get('/users', [AdminController::class, 'manageUsers'])->name('admin.users');
    Route::put('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.update-role');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    Route::get('/schedules', [AdminController::class, 'schedules'])->name('admin.schedules');
    Route::post('/schedules/{barber_id}/update', [AdminController::class, 'updateSchedule'])->name('admin.schedules.update');

    Route::get('/absences', [AdminController::class, 'absences'])->name('admin.absences');
    Route::post('/absences', [AdminController::class, 'storeAbsence'])->name('admin.absences.store');
    Route::delete('/absences/{id}', [AdminController::class, 'deleteAbsence'])->name('admin.absences.delete');

    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

});

require __DIR__.'/auth.php';