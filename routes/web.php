<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::get('/class/{classModel}', [ClassController::class, 'show'])->name('classes.show');
    Route::get('/users/{userType}', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/change-user-type', [UserController::class, 'changeUserType']);

    Route::get('/profile', function() {return;})->name('users.show');
});


require __DIR__.'/auth.php';
