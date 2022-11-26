<?php

use App\Http\Controllers\Admin\AdminController;
//use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\FixController;
use App\Http\Controllers\Admin\ManufacturerController;
use App\Http\Controllers\Admin\CarModelController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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

Route::get('/', function () {
    \App\Models\Admin::query()->create(['name' => 'Admin',
'email' => 'admin@email.com',
'mobile' => '1234567890',
'password' => bcrypt('123456')]);
    return 'welcome <a href="'.url('admin/files').'">test</a>';
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::group(['prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {
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


        Route::group(['prefix' => 'categories'], function () {
//        Route::group(['prefix' => 'categories', 'middleware' => ['permission:categories']], function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::put('/{category}', [CategoryController::class, 'update']);
            Route::delete('/{category}', [CategoryController::class, 'destroy']);
            Route::get('/indexTable', [CategoryController::class, 'indexTable']);
        });
        Route::group(['prefix' => 'manufacturers'], function () {
//        Route::group(['prefix' => 'categories', 'middleware' => ['permission:categories']], function () {
            Route::get('/', [ManufacturerController::class, 'index']);
            Route::post('/', [ManufacturerController::class, 'store']);
            Route::put('/{category}', [ManufacturerController::class, 'update']);
            Route::delete('/{category}', [ManufacturerController::class, 'destroy']);
            Route::get('/indexTable', [ManufacturerController::class, 'indexTable']);
        });
        Route::group(['prefix' => 'car_models'], function () {
//        Route::group(['prefix' => 'categories', 'middleware' => ['permission:categories']], function () {
            Route::get('/', [CarModelController::class, 'index']);
            Route::post('/', [CarModelController::class, 'store']);
            Route::put('/{category}', [CarModelController::class, 'update']);
            Route::delete('/{category}', [CarModelController::class, 'destroy']);
            Route::get('/indexTable', [CarModelController::class, 'indexTable']);
        });
        Route::group(['prefix' => 'files'], function () {
//        Route::group(['prefix' => 'categories', 'middleware' => ['permission:categories']], function () {
            Route::get('/', [FileController::class, 'index']);
            Route::post('/', [FileController::class, 'store']);
            Route::put('/{category}', [FileController::class, 'update']);
            Route::delete('/{category}', [FileController::class, 'destroy']);
            Route::get('/indexTable', [FileController::class, 'indexTable']);
        });
        Route::group(['prefix' => 'admins'], function () {
//        Route::group(['prefix' => 'categories', 'middleware' => ['permission:categories']], function () {
            Route::get('/', [AdminController::class, 'index']);
            Route::post('/', [AdminController::class, 'store']);
            Route::put('/{category}', [AdminController::class, 'update']);
            Route::delete('/{category}', [AdminController::class, 'destroy']);
            Route::get('/indexTable', [AdminController::class, 'indexTable']);
        });
        Route::group(['prefix' => 'users'], function () {
//        Route::group(['prefix' => 'categories', 'middleware' => ['permission:categories']], function () {
            Route::get('/', [UserController::class, 'index']);
            Route::post('/', [UserController::class, 'store']);
            Route::put('/{category}', [UserController::class, 'update']);
            Route::delete('/{category}', [UserController::class, 'destroy']);
            Route::get('/indexTable', [UserController::class, 'indexTable']);
        });
        Route::group(['prefix' => 'fixes'], function () {
//        Route::group(['prefix' => 'categories', 'middleware' => ['permission:categories']], function () {
            Route::get('/', [FixController::class, 'index']);
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
