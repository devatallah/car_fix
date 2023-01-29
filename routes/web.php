<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ECUController;
use App\Http\Controllers\Admin\ECUFileController;
use App\Http\Controllers\Admin\ECUFileRecordsController;
use App\Http\Controllers\Admin\ECURequestController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SolutionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\Admin\Auth\LoginController;


Route::get('/is_true', function (Request $request) {
$file_user=file_get_contents('dpf kiaaa.bin');
$file_fix=file_get_contents('egr kiaaa.bin');
$origin_file = file_get_contents('Kia_Sp.bin');


$result ='';
$file_user_new ='';
$file_fix_new='';
$map = array();
//dd( strlen( $file_user ));
for($i=0; $i < strlen( $file_fix ); $i++){
if($file_fix[$i]!= $origin_file[$i]){
    $map[$i]=$file_fix[$i];
}
}

for($i=0; $i < strlen($file_user); $i++){
    if(!empty($map[$i])){
        $result .=$map[$i];
    }else{
        $result .= $file_user[$i];
    }
}
$ready_result=file_get_contents('KIA_dpf_egr_off.bin');
   if($result == $ready_result){
    echo 'done';
   }else {
    echo 'done AA';
   }
});

Route::get(
    '/test_psa',
    function (Request $request) {
        $user_file = file_get_contents('testa.bin');
        $file1 = file_get_contents('origin_PSA.bin');
        $file2 = file_get_contents('dpf_PSA.bin');
        $file3 = file_get_contents('egr_PSA.bin');
        $file4 = file_get_contents('SCR_PSA.bin');
        $result = '';
        //file size must equl in all files
        if ($user_file === $file1 or $user_file === $file2 or $user_file === $file3 or $user_file === $file4) {
            if (filesize("testa.bin") === filesize("origin_PSA.bin")) {
                for ($i = 0; $i < strlen($file1); $i++) {
                    if ($file1[$i] === $file2[$i] && $file2[$i] === $file3[$i] && $file3[$i] === $file4[$i]) {
                        $result .= $file1[$i];
                    } elseif ($file2[$i] != $file1[$i]) {
                        $result .= $file2[$i];
                        // Handle differences between the files
                    } elseif ($file3[$i] != $file1[$i]) {
                        $result .= $file3[$i];
                        // Handle differences between the files
                    } elseif ($file4[$i] != $file1[$i]) {
                        $result .= $file4[$i];
                        // Handle differences between the files
                    }
                }
                file_put_contents('dd.bin', $result);
            }
        }
    }
);

Route::get('/test1', function (Request $request) {
    $file1 = file_get_contents('origin.bin');
    $file2 = file_get_contents('dpf.bin');
    $file3 = file_get_contents('egr.bin');

    $result = '';

    for ($i = 0; $i < strlen($file1); $i++) {
        if ($file1[$i] === $file2[$i] && $file2[$i] === $file3[$i]) {
            $result .= $file1[$i];
        } elseif ($file2[$i] != $file1[$i]) {
            $result .= $file2[$i];
            // Handle differences between the files
        } elseif ($file3[$i] != $file1[$i]) {
            $result .= $file3[$i];
            // Handle differences between the files
        }
    }

    file_put_contents('result1.bin', $result);
});

Route::get('/test2', function (Request $request) {
    $file1 = file_get_contents('origin.bin');
    $file2 = file_get_contents('dpf.bin');
    $file3 = file_get_contents('egr.bin');

    $result = $file1 . $file2 . $file3;

    file_put_contents('result2.bin', $result);
});

Route::get('/test3', function (Request $request) {
    $file1 = fopen('origin.bin', 'rb');
    $file2 = fopen('dpf.bin', 'rb');
    $file3 = fopen('egr.bin', 'rb');
    $result = fopen('result3.bin', 'wb');

    while (($data = fread($file1, 8192)) !== false) {
        fwrite($result, $data);
    }

    while (($data = fread($file2, 8192)) !== false) {
        fwrite($result, $data);
    }

    while (($data = fread($file3, 8192)) !== false) {
        fwrite($result, $data);
    }

    fclose($file1);
    fclose($file2);
    fclose($file3);
    fclose($result);
});

Route::get('/test4', function (Request $request) {

    // Open the three files in binary mode
    $f1 = fopen("origin.bin", "rb");
    $f2 = fopen("dpf.bin", "rb");
    $f3 = fopen("egr.bin", "rb");

    // Open the result file in binary mode
    $result = fopen("result4.bin", "wb");

    // Read the first line from each file
    $line1 = fgets($f1);
    $line2 = fgets($f2);
    $line3 = fgets($f3);

    // Initialize a counter for the line number
    $line_number = 1;

    // Loop until all lines have been read
    while ($line1 !== false || $line2 !== false || $line3 !== false) {
        // Compare the lines and print a message if they are different
        if ($line1 != $line2) {
            echo "Line $line_number: Files 1 and 2 are different\n";
        }
        if ($line1 != $line3) {
            echo "Line $line_number: Files 1 and 3 are different\n";
        }
        if ($line2 != $line3) {
            echo "Line $line_number: Files 2 and 3 are different\n";
        }

        // Write the lines to the result file
        fwrite($result, $line1);
        fwrite($result, $line2);
        fwrite($result, $line3);

        // Read the next line from each file
        $line1 = fgets($f1);
        $line2 = fgets($f2);
        $line3 = fgets($f3);

        // Increment the line number
        $line_number++;
    }

    // Close the files
    fclose($f1);
    fclose($f2);
    fclose($f3);
    fclose($result);
});

Route::get('/diff', function (Request $request) {
    dd(urlencode('https://carfix22.s3-eu-west-1.amazonaws.com/origin/magicSolution (FORD  (TVA) (NoChk) 1671625481.jpg'));
    $file = file_get_contents(
        str_replace(' ', '%20', "https://carfix22.s3-eu-west-1.amazonaws.com/origin/magicSolution (FORD  (TVA) (NoChk) 1671625481.jpg")
    );
    dd(md5($file));
    //    dd(file_get_contents(''.$origin_file));
    $md5image1 = md5(file_get_contents('https://carfix22.s3-eu-west-1.amazonaws.com/origin/magicSolution%20(brand%201%20%20solution%202)%20(NoChk)%201671622543.jpg'));
    $md5image2 = md5(file_get_contents('https://carfix22.s3-eu-west-1.amazonaws.com/fixed/magicSolution%20(brand%201%20%20solution%202)%20(NoChk)%201671622574.jpg'));
    dd($md5image1 == $md5image2);
});

Route::get('/get_module_brands', function (Request $request) {
    $module = \App\Models\Module::query()->whereHas('brands', function ($query) {
        $query->whereHas('ecus');
    })->with(['brands' => function ($query) {
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

Route::view('/', 'landing');

// Route::get('/', function () {
//     return view('index');
//     \App\Models\Admin::query()->create([
//         'name' => 'Admin',
//         'email' => 'admin@email.com',
//         'mobile' => '1234567890',
//         'password' => bcrypt('123456')
//     ]);
//     return 'welcome <a href="' . url('admin/files') . '">test</a>';
// });


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
    Route::group(['prefix' => 'ecu_files'], function () {
        Route::get('/', [ECUFileController::class, 'index']);
        Route::post('/file', [ECUFileController::class, 'store']);
        Route::put('/file/{uuid}', [ECUFileController::class, 'update']);
        Route::delete('/file/{uuid}', [ECUFileController::class, 'destroy']);
        Route::get('/indexTable/{ecu_uuid}', [ECUFileController::class, 'indexTable']);
    });
    Route::group(['prefix' => 'ecu_file_records'], function () {
        Route::get('/', [ECUFileRecordsController::class, 'index']);
        Route::post('/record', [ECUFileRecordsController::class, 'store']);
        Route::put('/record/{uuid}', [ECUFileRecordsController::class, 'update']);
        Route::delete('/record/{uuid}', [ECUFileRecordsController::class, 'destroy']);
        Route::get('/indexTable/{ecu_file_uuid}', [ECUFileRecordsController::class, 'indexTable']);
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
    // Route::group(['prefix' => 'solutions'], function () {
    //     Route::get('/', [SolutionController::class, 'index']);
    //     Route::get('/create', [SolutionController::class, 'create']);
    //     Route::post('/', [SolutionController::class, 'store']);
    //     Route::put('/{category}', [SolutionController::class, 'update']);
    //     Route::delete('/{category}', [SolutionController::class, 'destroy']);
    //     Route::get('/indexTable', [SolutionController::class, 'indexTable']);
    // });
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
        Route::get('/brands/list', [App\Http\Controllers\User\SolutionController::class, 'get_brands']);
        Route::post('/find/solution', [App\Http\Controllers\User\SolutionController::class, 'find_solution']);
    });
    Route::group(['prefix' => 'ecu_requests'], function () {
        Route::get('/', [App\Http\Controllers\User\ECURequestController::class, 'index']);
        Route::post('/', [App\Http\Controllers\User\ECURequestController::class, 'store']);
        Route::put('/{ecu_request}', [App\Http\Controllers\User\ECURequestController::class, 'update']);
        Route::delete('/{ecu_request}', [App\Http\Controllers\User\ECURequestController::class, 'destroy']);
        Route::get('/indexTable', [App\Http\Controllers\User\ECURequestController::class, 'indexTable']);
    });
});
