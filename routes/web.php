<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationServiceController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin,receptionist')->group(function () {
        Route::resource('guests', GuestController::class);

        Route::get('/guest-search', [ReservationController::class, 'guestSearch'])->name('guests.search');

        Route::get('reservations/{reservation}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('reservations/{reservation}/payments', [PaymentController::class, 'store'])->name('payments.store');

        Route::resource('reservations', ReservationController::class);
        Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.check-in');
        Route::post('reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.check-out');
        Route::get('reservations/{reservation}/invoice', [ReservationController::class, 'invoice'])->name('reservations.invoice');

        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    });

    Route::middleware('role:admin,housekeeping')->group(function () {
        Route::resource('rooms', RoomController::class);
        Route::post('rooms/{room}/mark-available', [RoomController::class, 'markAvailable'])->name('rooms.mark-available');
        Route::post('rooms/{room}/mark-cleaning', [RoomController::class, 'markCleaning'])->name('rooms.mark-cleaning');
        Route::post('rooms/{room}/mark-maintenance', [RoomController::class, 'markMaintenance'])->name('rooms.mark-maintenance');

        Route::resource('reservation-services', ReservationServiceController::class);
        Route::resource('services', ServiceController::class);
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('room-types', RoomTypeController::class);
        Route::resource('staff', StaffController::class);
    });
    Route::get('/room-board', [RoomController::class, 'board'])->name('rooms.board');
});
