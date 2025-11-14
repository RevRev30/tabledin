<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StaffReservationController;
use App\Http\Controllers\StaffRestaurantController;
use App\Http\Controllers\StaffCustomerController;
use App\Http\Controllers\SeatingController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Landing page
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'staff' || $user->role === 'admin') {
            return redirect()->route('staff.dashboard');
        }
    }
    return view('landing');
})->name('landing');

// Dashboard route - redirect to landing (always send /dashboard to the public root)
Route::get('/dashboard', function () {
    return redirect()->route('landing');
})->name('dashboard');

// Customer routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Added: show password form (named profile.password) so route('profile.password') resolves
    Route::get('/profile/password', function () {
        if (view()->exists('profile.password')) {
            return view('profile.password');
        }
        return response('Profile password page', 200);
    })->name('profile.password');

    // Added: update password handler
    Route::patch('/profile/password', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();
        if (! \Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated.');
    })->name('profile.password.update');

    // Restaurants
    Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
    
    // Reservations
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create/{restaurant}', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
    Route::patch('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    
    // Customer confirmation routes
    Route::get('/reservations/{reservation}/customer-confirm', [ReservationController::class, 'customerConfirm'])->name('reservations.customer-confirm');
    Route::get('/reservations/{reservation}/customer-cancel', [ReservationController::class, 'customerCancel'])->name('reservations.customer-cancel');

    // Add: POST alias used by the UI cancel form
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'customerCancel'])->name('reservations.cancel');
});

// Public signed confirmation routes (no login required)
Route::get('/reservations/{reservation}/confirm', [ReservationController::class, 'publicConfirm'])->name('reservations.public-confirm');
Route::get('/reservations/{reservation}/cancel', [ReservationController::class, 'publicCancel'])->name('reservations.public-cancel');

// Staff routes
Route::middleware(['auth', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    
    // Reservations
    Route::get('/reservations', [StaffReservationController::class, 'index'])->name('reservations');
    Route::patch('/reservations/{reservation}/status', [StaffReservationController::class, 'updateStatus'])->name('reservations.updateStatus');
    Route::put('/reservations/{reservation}/status', [StaffReservationController::class, 'updateStatus'])->name('reservations.status');
    Route::post('/reservations/{reservation}/approve', [StaffReservationController::class, 'approve'])->name('reservations.approve');
    Route::post('/reservations/{reservation}/complete', [StaffReservationController::class, 'complete'])->name('reservations.complete');
    Route::post('/reservations/{reservation}/cancel', [StaffReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::get('/reservations/{reservation}', [StaffReservationController::class, 'show'])->name('reservations.show');
    
    // Restaurants
    Route::get('/restaurants', [StaffRestaurantController::class, 'index'])->name('restaurants');

    // Customers
    Route::get('/customers', [StaffCustomerController::class, 'index'])->name('customers');

    // Seating
    Route::get('/seating', [SeatingController::class, 'index'])->name('seating.index');
    Route::get('/seating/advanced', [SeatingController::class, 'advanced'])->name('seating.advanced');
    Route::get('/seating/data', [SeatingController::class, 'getSeatingData'])->name('seating.data');
    Route::post('/seating/assign', [SeatingController::class, 'assignTable'])->name('seating.assign');
    Route::post('/seating/reassign', [SeatingController::class, 'reassignTable'])->name('seating.reassign');
    Route::post('/seating/update-status', [SeatingController::class, 'updateTableStatus'])->name('seating.updateStatus');
    Route::post('/seating/reservations/{reservation}/assign', [SeatingController::class, 'assignTable'])->name('seating.reservations.assign');
    Route::post('/seating/reservations/{reservation}/reassign', [SeatingController::class, 'reassignTable'])->name('seating.reservations.reassign');

    // Walk-in Queue Management
    Route::get('/queue', [QueueController::class, 'index'])->name('queue.index'); // expects restaurant_id
    Route::post('/queue/enqueue', [QueueController::class, 'enqueue'])->name('queue.enqueue');
    Route::post('/queue/call-next', [QueueController::class, 'callNext'])->name('queue.callNext');
    Route::post('/queue/mark-seated', [QueueController::class, 'markSeated'])->name('queue.markSeated');
    Route::post('/queue/cancel', [QueueController::class, 'cancel'])->name('queue.cancel');
    Route::post('/queue/seat-to-table', [QueueController::class, 'seatToTable'])->name('queue.seatToTable');
});

require __DIR__.'/auth.php';
