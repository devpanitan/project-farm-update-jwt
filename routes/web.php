<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmController;

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

// When the root URL is accessed, use the 'index' method of FarmController
Route::get('/', [FarmController::class, 'index']);

Route::get('/test-db', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        return '<h1>Database Connection Successful!</h1><p>Successfully connected to the database: ' . \Illuminate\Support\Facades\DB::connection()->getDatabaseName() . '</p>';
    } catch (\Exception $e) {
        return '<h1>Database Connection Failed</h1><p>Error: ' . $e->getMessage() . '</p>';
    }
});
