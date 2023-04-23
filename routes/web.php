<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return Inertia::render('Welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);
    return Inertia::render('Auth/Login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('products', ProductController::class);
    Route::resource('sales', SaleController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('users', App\Http\Controllers\UserController::class);

    Route::get('/api/reports/sales', [ReportController::class, 'getSalesReport'])->name('getSalesReport');
    Route::get('/current-user', [App\Http\Controllers\UserController::class, 'getCurrentUser'])->name('getCurrentUser');

    //Dashboard
    Route::get('/api/get-top-products', [ProductController::class, 'getTopProducts'])->name('getTopProducts');
    Route::get('/api/get-top-customers', [SaleController::class, 'getTopCustomers'])->name('getTopCustomers');
    Route::get('/api/get-recent-customers', [SaleController::class, 'getRecentCustomers'])->name('getRecentCustomers');
    Route::get('/api/get-today-sales', [SaleController::class, 'getTodaySales'])->name('getTodaySales');
    Route::get('/api/get-products-count', [ProductController::class, 'getProductCounts'])->name('getProductCounts');
    Route::get('/api/get-categories-count', [CategoryController::class, 'getCategoryCounts'])->name('getCategoryCounts');

    //getUsers
    Route::get('/api/get-users', [App\Http\Controllers\UserController::class, 'getUsers'])->name('getUsers');
});

require __DIR__ . '/auth.php';
