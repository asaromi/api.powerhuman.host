<?php

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\API\{CompanyController, EmployeeController, ResponsibilityController, RoleController, TeamController, UserController};
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
        Route::delete('/{id}', [RoleController::class, 'deleteRole']);
    });

    Route::group(['prefix' => 'responsibilities'], function () {
        Route::get('/', [ResponsibilityController::class, 'fetchResponsibilities']);
        Route::post('/', [ResponsibilityController::class, 'createResponsibility']);
        Route::delete('/{id}', [ResponsibilityController::class, 'deleteResponsibility']);
    });

    Route::group(['prefix' => 'employees'], function () {
        Route::get('/', [EmployeeController::class, 'fetchEmployees']);
        Route::post('/', [EmployeeController::class, 'createEmployee']);
        Route::post('/{id}', [EmployeeController::class, 'updateEmployee']);
        Route::delete('/{id}', [EmployeeController::class, 'deleteEmployee']);
    });
});
