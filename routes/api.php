<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmCategoryController;
use App\Http\Controllers\FarmController; 
use App\Http\Controllers\IotDeviceController;
use App\Http\Controllers\ActuatorCommandController;
use App\Http\Controllers\SensorTypeController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserFarmController;
use App\Http\Controllers\AutoRuleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\MQTTController;
use App\Http\Controllers\Api\ActivityLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes (Public)
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

// Protected API routes (Requires Authentication)
Route::group(['middleware' => 'auth:api'], function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Resource routes
    Route::apiResource('farm-categories', FarmCategoryController::class);
    Route::apiResource('farms', FarmController::class);
    Route::apiResource('iot-devices', IotDeviceController::class);
    Route::apiResource('actuator-commands', ActuatorCommandController::class);
    Route::apiResource('sensor-types', SensorTypeController::class);
    Route::apiResource('user-roles', UserRoleController::class);
    Route::apiResource('sensor-data', SensorDataController::class);
    Route::apiResource('users', UsersController::class);
    Route::apiResource('user-farms', UserFarmController::class);
    Route::apiResource('auto-rules', AutoRuleController::class);
    Route::apiResource('mqtt', MQTTController::class);
    Route::apiResource('activity-logs', ActivityLogController::class)->only(['index', 'store']);

    // Custom routes
    Route::get('users/{id}/farms', [UsersController::class, 'showFarms']);
    Route::post('/mqtt/publish', [MQTTController::class, 'publishMessage']);
});