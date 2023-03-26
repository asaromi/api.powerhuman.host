<?php

use App\Http\Controllers\API\{CompanyController, UserController};
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

Route::post('/auth/login', [UserController::class, 'login']);
Route::post('/auth/register', [UserController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::get('/', [UserController::class, 'fetch']);
        Route::post('/logout', [UserController::class, 'logout']);
    });

    Route::group(['prefix' => 'company'], function () {
        Route::get('/', [CompanyController::class, 'all']);
    });
});