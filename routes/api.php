<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//////////////////////////////////////////////////////////////////
///////////// START OF AUTH ROUTES///////////////
/////////////////////////////////////////////////////////////////
Route::group(['prefix' => 'auth', 'middleware' => 'throttle:login_register'], function($router) {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegistrationController::class, 'register']);
});
///////////// END OF AUTH ROUTES ////////////////////////////////

//////////////////////////////////////////////////////////////////
///////////// START OF USER ROUTES///////////////
/////////////////////////////////////////////////////////////////
Route::group(['prefix' => 'user', 'middleware' => 'auth:api'], function($router) {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::patch('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});
