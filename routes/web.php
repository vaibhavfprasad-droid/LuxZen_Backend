<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController; // Only this controller is needed for the dashboard now

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect the root URL to the dashboard if logged in, otherwise to login.
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication routes (e.g., /login, /logout) with registration disabled.
Auth::routes(['register' => false]);

// --- Protected Admin Panel Routes ---
// All routes that require a user to be logged in should be in this group.
Route::middleware(['auth'])->group(function () {
    
    // The new, correct dashboard route using your DashboardController.
    // This will fetch the data for the KPIs and charts.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Other authenticated page routes
    Route::get('/users', fn() => view('pages.users-index'))->name('users.index');
    Route::get('/trips', fn() => view('pages.trips-index'))->name('trips.index');
    Route::get('/vehicles', fn() => view('pages.vehicles-index'))->name('vehicles.index');
    Route::get('/invoices', fn() => view('pages.invoices-index'))->name('invoices.index');
    Route::get('/settings', fn() => view('pages.settings-index'))->name('settings');
});