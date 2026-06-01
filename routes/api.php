<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BalanceLogController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\ECURequestController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserPortalController;
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
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/user/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    /************** User Routes **************/
    Route::post('/user/auth/check', [AuthController::class, 'checkAuth']);
    Route::post('/user/auth/logout', [AuthController::class, 'logout']);

    Route::post('/user/balance/log', [BalanceLogController::class, 'balanceLog']);
    Route::post('/user/balance/update', [BalanceLogController::class, 'updateBalance']);

    Route::post('/user/ecu/request', [ECURequestController::class, 'store']);

    /************** User Portal Routes (New) **************/
    Route::get('/user/portal/brands', [UserPortalController::class, 'getBrands']);
    Route::post('/user/portal/ecus', [UserPortalController::class, 'getEcusByBrand']);
    Route::post('/user/portal/solutions', [UserPortalController::class, 'getSolutionsByEcu']);
    Route::post('/user/portal/process-file', [UserPortalController::class, 'processFile']);

    Route::post('/brands', [DataController::class, 'brands']);

    Route::post('/scripts', [DataController::class, 'scripts']);
    Route::post('/solution_templates', [DataController::class, 'solutionTemplates']);
    Route::post('/dtc', [DataController::class, 'dtc']);
    Route::post('/dtc/brands', [DataController::class, 'dtc_brands']);
});

Route::get('/user/portal/download-file', [UserPortalController::class, 'downloadFile'])->name('api.download-file');
Route::middleware('checkApiToken')->group(function() {
    Route::post('/create/user', [UserController::class, 'create']);
    Route::post('/update/user/balance', [UserController::class, 'updateBalance']);
    });
    