<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationServiceController;
use App\Http\Controllers\ReservationTaxController;
use App\Http\Controllers\RestaurantOrderController;
use App\Http\Controllers\RestaurantOrderTaxController;
use App\Http\Controllers\RestaurantPaymentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TaxController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin,receptionist')->group(function () {});
    Route::resource('guests', GuestController::class);

    Route::get('/guest-search', [ReservationController::class, 'guestSearch'])->name('guests.search');

    Route::get('reservations/{reservation}/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('reservations/{reservation}/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::resource('reservations', ReservationController::class);
    Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.check-in');
    Route::post('reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.check-out');
    Route::get('reservations/{reservation}/invoice', [ReservationController::class, 'invoice'])->name('reservations.invoice');

    Route::get('reservations/{reservation}/taxes/create', [ReservationTaxController::class, 'create'])->name('reservation-taxes.create');
    Route::post('reservations/{reservation}/taxes', [ReservationTaxController::class, 'store'])->name('reservation-taxes.store');
    Route::delete('reservations/{reservation}/taxes/{reservationTax}', [ReservationTaxController::class, 'destroy'])->name('reservation-taxes.destroy');

    Route::resource('restaurant-orders', RestaurantOrderController::class);
    Route::get('restaurant-orders/{restaurantOrder}/payments/create', [RestaurantPaymentController::class, 'create'])->name('restaurant-payments.create');
    Route::post('restaurant-orders/{restaurantOrder}/payments', [RestaurantPaymentController::class, 'store'])->name('restaurant-payments.store');
    Route::get('restaurant-orders/{restaurantOrder}/invoice', [RestaurantOrderController::class, 'invoice'])->name('restaurant-orders.invoice');
    Route::get('restaurant-orders/{restaurantOrder}/taxes/create', [RestaurantOrderTaxController::class, 'create'])->name('restaurant-order-taxes.create');
    Route::post('restaurant-orders/{restaurantOrder}/taxes', [RestaurantOrderTaxController::class, 'store'])->name('restaurant-order-taxes.store');
    Route::delete('restaurant-orders/{restaurantOrder}/taxes/{restaurantOrderTax}', [RestaurantOrderTaxController::class, 'destroy'])->name('restaurant-order-taxes.destroy');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

    Route::get('/room-board', [RoomController::class, 'board'])->name('rooms.board');
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

    Route::post('rooms/{room}/mark-available', [RoomController::class, 'markAvailable'])->name('rooms.mark-available');
    Route::post('rooms/{room}/mark-cleaning', [RoomController::class, 'markCleaning'])->name('rooms.mark-cleaning');
    Route::post('rooms/{room}/mark-maintenance', [RoomController::class, 'markMaintenance'])->name('rooms.mark-maintenance');

    Route::resource('reservation-services', ReservationServiceController::class);
    Route::resource('services', ServiceController::class);

    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    Route::resource('room-types', RoomTypeController::class);
    Route::resource('staff', StaffController::class);
    Route::resource('taxes', TaxController::class);
    Route::resource('menu-categories', MenuCategoryController::class);
    Route::resource('menu-items', MenuItemController::class);
});
