<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return '<h1>Database Connection Successful!</h1><p>Successfully connected to the database: ' . DB::connection()->getDatabaseName() . '</p>';
    } catch (\Exception $e) {
        return '<h1>Database Connection Failed</h1><p>Error: ' . $e->getMessage() . '</p>';
    }
});
