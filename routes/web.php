<?php

use App\Http\Controllers\Admin\AdminController;
//use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\SolutionController;
use App\Http\Controllers\Admin\ECUController;
use App\Http\Controllers\Admin\FixController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CarModelController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| web routes
|--------------------------------------------------------------------------
|
| here is where you can register web routes for your application. these
| routes are loaded by the routeserviceprovider within a group which
| contains the "web" middleware group. now create something great!
|
*/

Route::get('/get_solution_brands', function (Request $request) {
    $solution = \App\Models\Solution::query()->with('brands')->find($request->solution_uuid);
    $json = [];
    foreach ($solution->brands as $brand) {
        $json[] = [
            'id' => $brand->uuid,
            'text' => $brand->name,
        ];
    }
    return response()->json($json);
});
Route::get('/get_solution_brand_ecus', function (Request $request) {
    $ecus = \App\Models\ECU::query()->where(['solution_uuid' => $request->solution_uuid, 'brand_uuid' => $request->brand_uuid])->get();
    $json = [];
    foreach ($ecus as $ecu) {
        $json[] = [
            'id' => $ecu->uuid,
            'text' => $ecu->name,
        ];
    }
    return response()->json($json);
});
Route::get('/', function () {
    dd(\App\Models\Solution::query()->first()->brands);
    \App\Models\Admin::query()->create(['name' => 'Admin',
'email' => 'admin@email.com',
'mobile' => '1234567890',
'password' => bcrypt('123456')]);
    return 'welcome <a href="'.url('admin/files').'">test</a>';
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
    Route::get('admin/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('admin/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'login'])->name('admin_login');
    Route::post('admin/logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('admin_logout');
    Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth:admin']], function () {
        Route::get('/', function () {
            return view('portals.admin.app');
        });
        Route::get('/profile', function () {
            return view('portals.admin.profile');
        });
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/password', [ProfileController::class, 'changePassword']);


        Route::group(['prefix' => 'solutions'], function () {
            Route::get('/', [SolutionController::class, 'index']);
            Route::post('/', [SolutionController::class, 'store']);
            Route::put('/{solution}', [SolutionController::class, 'update']);
            Route::delete('/{solution}', [SolutionController::class, 'destroy']);
            Route::get('/indexTable', [SolutionController::class, 'indexTable']);
        });
        Route::group(['prefix' => 'brands'], function () {
            Route::get('/', [BrandController::class, 'index']);
            Route::post('/', [BrandController::class, 'store']);
            Route::put('/{brand}', [BrandController::class, 'update']);
            Route::delete('/{brand}', [BrandController::class, 'destroy']);
            Route::get('/indexTable', [BrandController::class, 'indexTable']);
        });
        Route::group(['prefix' => 'ecus'], function () {
            Route::get('/', [ECUController::class, 'index']);
            Route::post('/', [ECUController::class, 'store']);
            Route::put('/{ecu}', [ECUController::class, 'update']);
            Route::delete('/{ecu}', [ECUController::class, 'destroy']);
            Route::get('/indexTable', [ECUController::class, 'indexTable']);
        });
        Route::group(['prefix' => 'admins'], function () {
            Route::get('/', [AdminController::class, 'index']);
            Route::post('/', [AdminController::class, 'store']);
            Route::put('/{admin}', [AdminController::class, 'update']);
            Route::delete('/{admin}', [AdminController::class, 'destroy']);
            Route::get('/indexTable', [AdminController::class, 'indexTable']);
        });
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', [UserController::class, 'index']);
            Route::post('/', [UserController::class, 'store']);
            Route::put('/{user}', [UserController::class, 'update']);
            Route::delete('/{user}', [UserController::class, 'destroy']);
            Route::get('/indexTable', [UserController::class, 'indexTable']);
        });
        Route::group(['prefix' => 'fixes'], function () {
            Route::get('/', [FixController::class, 'index']);
            Route::get('/create', [FixController::class, 'create']);
            Route::post('/', [FixController::class, 'store']);
            Route::put('/{category}', [FixController::class, 'update']);
            Route::delete('/{category}', [FixController::class, 'destroy']);
            Route::get('/indexTable', [FixController::class, 'indexTable']);
        });
    });


    Route::get('user/login', [App\Http\Controllers\User\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('user/login', [App\Http\Controllers\User\Auth\LoginController::class, 'login'])->name('user_login');
    Route::post('user/logout', [App\Http\Controllers\User\Auth\LoginController::class, 'logout'])->name('user_logout');
    Route::group(['namespace' => 'User', 'prefix' => 'user', 'middleware' => ['auth:web']], function () {
        Route::get('/', function () {
            return redirect('user/fixes');
        });
        Route::get('/profile', function () {
            return view('portals.user.profile');
        });
        Route::put('/profile', [App\Http\Controllers\User\ProfileController::class, 'update']);
        Route::put('/password', [App\Http\Controllers\User\ProfileController::class, 'changePassword']);


        Route::group(['prefix' => 'fixes'], function () {
//        Route::group(['prefix' => 'categories', 'middleware' => ['permission:categories']], function () {
            Route::get('/', [App\Http\Controllers\User\FixController::class, 'index']);
            Route::post('/', [App\Http\Controllers\User\FixController::class, 'store']);
            Route::put('/{fix}', [App\Http\Controllers\User\FixController::class, 'update']);
            Route::delete('/{fix}', [App\Http\Controllers\User\FixController::class, 'destroy']);
            Route::get('/indexTable', [App\Http\Controllers\User\FixController::class, 'indexTable']);
        });
    });
});
