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

/*
|--------------------------------------------------------------------------
| Gate Middleware Helper
|--------------------------------------------------------------------------
|
| Produces:
| can:module-access,'reservations','read'
|
*/

$moduleAccess = static function (
    string $module,
    string $action
): string {
    return "can:module-access,'{$module}','{$action}'";
};

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () use ($moduleAccess) {
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/', function () {
        $user = auth()->user();
        $email = strtolower(trim($user->email));

        /*
        |--------------------------------------------------------------------------
        | Preferred landing pages
        |--------------------------------------------------------------------------
        |
        | A preferred page is used only when the account has permission to read it.
        | This prevents a 403 immediately after login.
        |
        */

        $preferredRoutes = [
            'hoteladmin@hotel.com' => [
                ['dashboard', 'read', 'dashboard'],
                ['reservations', 'read', 'reservations.index'],
                ['rooms', 'read', 'rooms.board'],
            ],
            'hotelmanager@hotel.com' => [
                ['dashboard', 'read', 'dashboard'],
                ['reservations', 'read', 'reservations.index'],
                ['rooms', 'read', 'rooms.board'],
            ],
            'restaurantadmin@hotel.com' => [
                ['restaurant-orders', 'read', 'restaurant-orders.index'],
                ['menu-items', 'read', 'menu-items.index'],
                ['menu-categories', 'read', 'menu-categories.index'],
            ],
            'restaurantmanager@hotel.com' => [
                ['restaurant-orders', 'read', 'restaurant-orders.index'],
                ['menu-items', 'read', 'menu-items.index'],
                ['menu-categories', 'read', 'menu-categories.index'],
            ],
        ];

        foreach ($preferredRoutes[$email] ?? [] as [$module, $action, $routeName]) {
            if ($user->can('module-access', [$module, $action])) {
                return redirect()->route($routeName);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | General permission fallback
        |--------------------------------------------------------------------------
        */

        $moduleRoutes = [
            ['dashboard', 'read', 'dashboard'],
            ['reservations', 'read', 'reservations.index'],
            ['rooms', 'read', 'rooms.board'],
            ['restaurant-orders', 'read', 'restaurant-orders.index'],
            ['guests', 'read', 'guests.index'],
            ['reservation-services', 'read', 'reservation-services.index'],
            ['services', 'read', 'services.index'],
            ['reports', 'read', 'reports.index'],
            ['room-types', 'read', 'room-types.index'],
            ['staff', 'read', 'staff.index'],
            ['taxes', 'read', 'taxes.index'],
            ['menu-categories', 'read', 'menu-categories.index'],
            ['menu-items', 'read', 'menu-items.index'],
        ];

        foreach ($moduleRoutes as [$module, $action, $routeName]) {
            if ($user->can('module-access', [$module, $action])) {
                return redirect()->route($routeName);
            }
        }

        abort(403, 'No readable module has been assigned to your account.');
    })->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware($moduleAccess('dashboard', 'read'))
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Guests
    |--------------------------------------------------------------------------
    */

    Route::get('/guest-search', [
        ReservationController::class,
        'guestSearch',
    ])
        ->middleware($moduleAccess('guests', 'read'))
        ->name('guests.search');

    Route::resource('guests', GuestController::class)
        ->middlewareFor(
            ['index', 'show'],
            $moduleAccess('guests', 'read')
        )
        ->middlewareFor(
            ['create', 'store'],
            $moduleAccess('guests', 'create')
        )
        ->middlewareFor(
            ['edit', 'update'],
            $moduleAccess('guests', 'update')
        )
        ->middlewareFor(
            'destroy',
            $moduleAccess('guests', 'delete')
        );

    /*
    |--------------------------------------------------------------------------
    | Reservations
    |--------------------------------------------------------------------------
    */

    Route::resource('reservations', ReservationController::class)
        ->middlewareFor(
            ['index', 'show'],
            $moduleAccess('reservations', 'read')
        )
        ->middlewareFor(
            ['create', 'store'],
            $moduleAccess('reservations', 'create')
        )
        ->middlewareFor(
            ['edit', 'update'],
            $moduleAccess('reservations', 'update')
        )
        ->middlewareFor(
            'destroy',
            $moduleAccess('reservations', 'delete')
        );

    Route::get(
        'reservations/{reservation}/payments/create',
        [PaymentController::class, 'create']
    )
        ->middleware($moduleAccess('reservations', 'create'))
        ->name('payments.create');

    Route::post(
        'reservations/{reservation}/payments',
        [PaymentController::class, 'store']
    )
        ->middleware($moduleAccess('reservations', 'create'))
        ->name('payments.store');

    Route::post(
        'reservations/{reservation}/check-in',
        [ReservationController::class, 'checkIn']
    )
        ->middleware($moduleAccess('reservations', 'update'))
        ->name('reservations.check-in');

    Route::post(
        'reservations/{reservation}/check-out',
        [ReservationController::class, 'checkOut']
    )
        ->middleware($moduleAccess('reservations', 'update'))
        ->name('reservations.check-out');

    Route::get(
        'reservations/{reservation}/invoice',
        [ReservationController::class, 'invoice']
    )
        ->middleware($moduleAccess('reservations', 'read'))
        ->name('reservations.invoice');

    /*
    |--------------------------------------------------------------------------
    | Reservation Taxes
    |--------------------------------------------------------------------------
    */

    Route::get(
        'reservations/{reservation}/taxes/create',
        [ReservationTaxController::class, 'create']
    )
        ->middleware($moduleAccess('reservations', 'create'))
        ->name('reservation-taxes.create');

    Route::post(
        'reservations/{reservation}/taxes',
        [ReservationTaxController::class, 'store']
    )
        ->middleware($moduleAccess('reservations', 'create'))
        ->name('reservation-taxes.store');

    Route::delete(
        'reservations/{reservation}/taxes/{reservationTax}',
        [ReservationTaxController::class, 'destroy']
    )
        ->middleware($moduleAccess('reservations', 'delete'))
        ->name('reservation-taxes.destroy');

    /*
    |--------------------------------------------------------------------------
    | Restaurant Orders
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'restaurant-orders',
        RestaurantOrderController::class
    )
        ->middlewareFor(
            ['index', 'show'],
            $moduleAccess('restaurant-orders', 'read')
        )
        ->middlewareFor(
            ['create', 'store'],
            $moduleAccess('restaurant-orders', 'create')
        )
        ->middlewareFor(
            ['edit', 'update'],
            $moduleAccess('restaurant-orders', 'update')
        )
        ->middlewareFor(
            'destroy',
            $moduleAccess('restaurant-orders', 'delete')
        );

    Route::get(
        'restaurant-orders/{restaurantOrder}/payments/create',
        [RestaurantPaymentController::class, 'create']
    )
        ->middleware($moduleAccess('restaurant-orders', 'create'))
        ->name('restaurant-payments.create');

    Route::post(
        'restaurant-orders/{restaurantOrder}/payments',
        [RestaurantPaymentController::class, 'store']
    )
        ->middleware($moduleAccess('restaurant-orders', 'create'))
        ->name('restaurant-payments.store');

    Route::get(
        'restaurant-orders/{restaurantOrder}/invoice',
        [RestaurantOrderController::class, 'invoice']
    )
        ->middleware($moduleAccess('restaurant-orders', 'read'))
        ->name('restaurant-orders.invoice');

    /*
    |--------------------------------------------------------------------------
    | Restaurant Order Taxes
    |--------------------------------------------------------------------------
    */

    Route::get(
        'restaurant-orders/{restaurantOrder}/taxes/create',
        [RestaurantOrderTaxController::class, 'create']
    )
        ->middleware($moduleAccess('restaurant-orders', 'create'))
        ->name('restaurant-order-taxes.create');

    Route::post(
        'restaurant-orders/{restaurantOrder}/taxes',
        [RestaurantOrderTaxController::class, 'store']
    )
        ->middleware($moduleAccess('restaurant-orders', 'create'))
        ->name('restaurant-order-taxes.store');

    Route::delete(
        'restaurant-orders/{restaurantOrder}/taxes/{restaurantOrderTax}',
        [RestaurantOrderTaxController::class, 'destroy']
    )
        ->middleware($moduleAccess('restaurant-orders', 'delete'))
        ->name('restaurant-order-taxes.destroy');

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get('/reports', [ReportsController::class, 'index'])
        ->middleware($moduleAccess('reports', 'read'))
        ->name('reports.index');

    /*
    |--------------------------------------------------------------------------
    | Rooms
    |--------------------------------------------------------------------------
    */

    Route::get('/room-board', [RoomController::class, 'board'])
        ->middleware($moduleAccess('rooms', 'read'))
        ->name('rooms.board');

    Route::post(
        'rooms/{room}/mark-available',
        [RoomController::class, 'markAvailable']
    )
        ->middleware($moduleAccess('rooms', 'update'))
        ->name('rooms.mark-available');

    Route::post(
        'rooms/{room}/mark-cleaning',
        [RoomController::class, 'markCleaning']
    )
        ->middleware($moduleAccess('rooms', 'update'))
        ->name('rooms.mark-cleaning');

    Route::post(
        'rooms/{room}/mark-maintenance',
        [RoomController::class, 'markMaintenance']
    )
        ->middleware($moduleAccess('rooms', 'update'))
        ->name('rooms.mark-maintenance');

    Route::resource('rooms', RoomController::class)
        ->middlewareFor(
            ['index', 'show'],
            $moduleAccess('rooms', 'read')
        )
        ->middlewareFor(
            ['create', 'store'],
            $moduleAccess('rooms', 'create')
        )
        ->middlewareFor(
            ['edit', 'update'],
            $moduleAccess('rooms', 'update')
        )
        ->middlewareFor(
            'destroy',
            $moduleAccess('rooms', 'delete')
        );

    /*
    |--------------------------------------------------------------------------
    | Reservation Services
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'reservation-services',
        ReservationServiceController::class
    )
        ->middlewareFor(
            ['index', 'show'],
            $moduleAccess('reservation-services', 'read')
        )
        ->middlewareFor(
            ['create', 'store'],
            $moduleAccess('reservation-services', 'create')
        )
        ->middlewareFor(
            ['edit', 'update'],
            $moduleAccess('reservation-services', 'update')
        )
        ->middlewareFor(
            'destroy',
            $moduleAccess('reservation-services', 'delete')
        );

    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    */

    Route::resource('services', ServiceController::class)
        ->middlewareFor(
            ['index', 'show'],
            $moduleAccess('services', 'read')
        )
        ->middlewareFor(
            ['create', 'store'],
            $moduleAccess('services', 'create')
        )
        ->middlewareFor(
            ['edit', 'update'],
            $moduleAccess('services', 'update')
        )
        ->middlewareFor(
            'destroy',
            $moduleAccess('services', 'delete')
        );

    /*
    |--------------------------------------------------------------------------
    | Room Types
    |--------------------------------------------------------------------------
    */

    Route::resource('room-types', RoomTypeController::class)
        ->middlewareFor(
            ['index', 'show'],
            $moduleAccess('room-types', 'read')
        )
        ->middlewareFor(
            ['create', 'store'],
            $moduleAccess('room-types', 'create')
        )
        ->middlewareFor(
            ['edit', 'update'],
            $moduleAccess('room-types', 'update')
        )
        ->middlewareFor(
            'destroy',
            $moduleAccess('room-types', 'delete')
        );

    /*
    |--------------------------------------------------------------------------
    | Staff
    |--------------------------------------------------------------------------
    */

    Route::resource('staff', StaffController::class)
        ->middlewareFor(
            ['index', 'show'],
            $moduleAccess('staff', 'read')
        )
        ->middlewareFor(
            ['create', 'store'],
            $moduleAccess('staff', 'create')
        )
        ->middlewareFor(
            ['edit', 'update'],
            $moduleAccess('staff', 'update')
        )
        ->middlewareFor(
            'destroy',
            $moduleAccess('staff', 'delete')
        );

    /*
    |--------------------------------------------------------------------------
    | Taxes
    |--------------------------------------------------------------------------
    */

    Route::resource('taxes', TaxController::class)
        ->middlewareFor(
            ['index', 'show'],
            $moduleAccess('taxes', 'read')
        )
        ->middlewareFor(
            ['create', 'store'],
            $moduleAccess('taxes', 'create')
        )
        ->middlewareFor(
            ['edit', 'update'],
            $moduleAccess('taxes', 'update')
        )
        ->middlewareFor(
            'destroy',
            $moduleAccess('taxes', 'delete')
        );

    /*
    |--------------------------------------------------------------------------
    | Menu Categories
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'menu-categories',
        MenuCategoryController::class
    )
        ->middlewareFor(
            ['index', 'show'],
            $moduleAccess('menu-categories', 'read')
        )
        ->middlewareFor(
            ['create', 'store'],
            $moduleAccess('menu-categories', 'create')
        )
        ->middlewareFor(
            ['edit', 'update'],
            $moduleAccess('menu-categories', 'update')
        )
        ->middlewareFor(
            'destroy',
            $moduleAccess('menu-categories', 'delete')
        );

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    */

    Route::resource('menu-items', MenuItemController::class)
        ->middlewareFor(
            ['index', 'show'],
            $moduleAccess('menu-items', 'read')
        )
        ->middlewareFor(
            ['create', 'store'],
            $moduleAccess('menu-items', 'create')
        )
        ->middlewareFor(
            ['edit', 'update'],
            $moduleAccess('menu-items', 'update')
        )
        ->middlewareFor(
            'destroy',
            $moduleAccess('menu-items', 'delete')
        );
});
