<?php

use Illuminate\Support\Facades\Route;

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

// Publicly accessible routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login'); // Naming the route for easy URL generation

// The conflicting GET /register route has been removed to prevent conflicts with the API.

// Route for the main dashboard, protected by client-side script
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Database connection test route (for debugging)
Route::get('/test-db', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        return '<h1>Database Connection Successful!</h1><p>Successfully connected to the database: ' . \Illuminate\Support\Facades\DB::connection()->getDatabaseName() . '</p>';
    } catch (\Exception $e) {
        return '<h1>Database Connection Failed</h1><p>Error: ' . $e->getMessage() . '</p>';
    }
});
