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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route for the FarmCategory resource
Route::apiResource('farm-categories', FarmCategoryController::class);

// Route for the new Farm resource
Route::apiResource('farms', FarmController::class);

// Route for the new IotDevice resource
Route::apiResource('iot-devices', IotDeviceController::class);

// Route for the new ActuatorCommand resource
Route::apiResource('actuator-commands', ActuatorCommandController::class);

// Route for the new SensorType resource
Route::apiResource('sensor-types', SensorTypeController::class);

// Route for the new UserRole resource
Route::apiResource('user-roles', UserRoleController::class);

// Route for the new SensorData resource
Route::apiResource('sensor-data', SensorDataController::class);

// Main User resource for registration and auth checking
Route::apiResource('users', UsersController::class);

// Route for the new UserFarm resource
Route::apiResource('user-farms', UserFarmController::class);

// Route for the new AutoRule resource
Route::apiResource('auto-rules', AutoRuleController::class);

// Route for the new MQTT resource
Route::apiResource('mqtt', MQTTController::class);
Route::post('/mqtt/publish', [MQTTController::class, 'publishMessage']);


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
