<?php

use App\Http\Controllers\API\{CompanyController, RoleController, TeamController, UserController};
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

    Route::group(['prefix' => 'companies'], function () {
        Route::get('/', [CompanyController::class, 'fetchCompanies']);
        Route::post('/', [CompanyController::class, 'createCompany']);
        Route::post('/{id}', [CompanyController::class, 'updateCompany']);
    });

    Route::group(['prefix' => 'teams'], function () {
        Route::get('/', [TeamController::class, 'fetchTeams']);
        Route::post('/', [TeamController::class, 'createTeam']);
        Route::post('/{id}', [TeamController::class, 'updateTeam']);
        Route::delete('/{id}', [TeamController::class, 'deleteTeam']); // not tested yet
    });

    Route::group(['prefix' => 'roles'], function() {
        Route::get('/', [RoleController::class, 'fetchRoles']);
        Route::post('/', [RoleController::class, 'createRole']);
        Route::post('/{id}', [RoleController::class, 'updateRole']);
        Route::delete('/{id}', [RoleController::class, 'deleteRole']); // not tested yet
    });
});
