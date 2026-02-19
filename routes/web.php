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

// The root path now returns a JSON response indicating the API is running.
Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Welcome to the Smart Farm API. The API is running correctly.'
    ]);
});

// The following web view routes are commented out as this is an API-only project.
/*
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
*/

// Database connection test route (for debugging)
Route::get('/test-db', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        return '<h1>Database Connection Successful!</h1><p>Successfully connected to the database: ' . \Illuminate\Support\Facades\DB::connection()->getDatabaseName() . '</p>';
    } catch (\Exception $e) {
        return '<h1>Database Connection Failed</h1><p>Error: ' . $e->getMessage() . '</p>';
    }
});
