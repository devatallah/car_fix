<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ECUController;
use App\Http\Controllers\Admin\ECURequestController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SolutionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\Admin\Auth\LoginController;



Route::get('/get_module_brands', function (Request $request) {
    $module = \App\Models\Module::query()->whereHas('brands', function ($query){
        $query->whereHas('ecus');
    })->with(['brands' => function($query){
        $query->whereHas('ecus')->with('ecus');
    }])->where('uuid', $request->module_uuid)->first();

    $main_list = [];
    $ecu_list = [];
    foreach ($module->brands as $brand) {
        foreach ($brand->ecus as $ecu) {
            $ecu_list[] = ['id' => $ecu->uuid, 'text' => $ecu->name];
        }
        $main_list[] = ['id' => $brand->uuid, 'text' => $brand->name, 'children' => $ecu_list];
    }
    return response()->json($main_list);
});
Route::get('/get_module_brand_ecus', function (Request $request) {

    $ecus = \App\Models\ECU::query()->where(['module_uuid' => $request->module_uuid, 'brand_uuid' => $request->brand_uuid])->get();
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
    return view('index');
    \App\Models\Admin::query()->create(['name' => 'Admin',
        'email' => 'admin@email.com',
        'mobile' => '1234567890',
        'password' => bcrypt('123456')]);
    return 'welcome <a href="' . url('admin/files') . '">test</a>';
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
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


    Route::group(['prefix' => 'modules'], function () {
        Route::get('/', [ModuleController::class, 'index']);
        Route::post('/', [ModuleController::class, 'store']);
        Route::put('/{module}', [ModuleController::class, 'update']);
        Route::delete('/{module}', [ModuleController::class, 'destroy']);
        Route::get('/indexTable', [ModuleController::class, 'indexTable']);
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
    Route::group(['prefix' => 'solutions'], function () {
        Route::get('/', [SolutionController::class, 'index']);
        Route::get('/create', [SolutionController::class, 'create']);
        Route::post('/', [SolutionController::class, 'store']);
        Route::put('/{category}', [SolutionController::class, 'update']);
        Route::delete('/{category}', [SolutionController::class, 'destroy']);
        Route::get('/indexTable', [SolutionController::class, 'indexTable']);
    });
    Route::group(['prefix' => 'ecu_requests'], function () {
        Route::get('/', [ECURequestController::class, 'index']);
        Route::post('/', [ECURequestController::class, 'store']);
        Route::put('/{ecu_request}', [ECURequestController::class, 'update']);
        Route::delete('/{ecu_request}', [ECURequestController::class, 'destroy']);
        Route::get('/indexTable', [ECURequestController::class, 'indexTable']);
    });
});


Route::get('user/login', [App\Http\Controllers\User\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('user/login', [App\Http\Controllers\User\Auth\LoginController::class, 'login'])->name('user_login');
Route::post('user/logout', [App\Http\Controllers\User\Auth\LoginController::class, 'logout'])->name('user_logout');
Route::group(['namespace' => 'User', 'prefix' => 'user', 'middleware' => ['auth:web']], function () {
    Route::get('/', function () {
        return redirect('user/solutions');
    });
    Route::get('/profile', function () {
        return view('portals.user.profile');
    });
    Route::put('/profile', [App\Http\Controllers\User\ProfileController::class, 'update']);
    Route::put('/password', [App\Http\Controllers\User\ProfileController::class, 'changePassword']);


    Route::group(['prefix' => 'solutions'], function () {
        Route::get('/', [App\Http\Controllers\User\SolutionController::class, 'index']);
        Route::post('/', [App\Http\Controllers\User\SolutionController::class, 'store']);
        Route::put('/{solution}', [App\Http\Controllers\User\SolutionController::class, 'update']);
        Route::delete('/{solution}', [App\Http\Controllers\User\SolutionController::class, 'destroy']);
        Route::get('/indexTable', [App\Http\Controllers\User\SolutionController::class, 'indexTable']);
    });
    Route::group(['prefix' => 'ecu_requests'], function () {
        Route::get('/', [App\Http\Controllers\User\ECURequestController::class, 'index']);
        Route::post('/', [App\Http\Controllers\User\ECURequestController::class, 'store']);
        Route::put('/{ecu_request}', [App\Http\Controllers\User\ECURequestController::class, 'update']);
        Route::delete('/{ecu_request}', [App\Http\Controllers\User\ECURequestController::class, 'destroy']);
        Route::get('/indexTable', [App\Http\Controllers\User\ECURequestController::class, 'indexTable']);
    });
});
