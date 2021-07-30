<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProfileController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['guest:api']], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/{user}/confirm', [AuthController::class, 'confirm']);
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::patch('/profile/{user}', [ProfileController::class, 'update']);
});