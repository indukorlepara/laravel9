<?php
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Register route
Route::post('register', [AuthController::class, 'register']);

// Login route to get the API token
Route::post('login', [AuthController::class, 'login']);

// Protected route, requires API token
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('user', [AuthController::class, 'getUsers']);
    Route::get('user/{id}', [AuthController::class, 'getUser']);
    Route::put('user/{id}', [AuthController::class, 'updateUser']);
    Route::delete('user/{id}', [AuthController::class, 'deleteUser']);
    Route::post('logout', [AuthController::class, 'logout']);
});
