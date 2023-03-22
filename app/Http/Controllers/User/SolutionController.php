<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\ECU;
use App\Models\ECUFile;
use App\Models\ECUFileRecord;
use App\Models\Module;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

ini_set('memory_limit', '1024M');
class SolutionController extends Controller
{

    public function index(Request $request)
    {
        $modules = Module::where('name', '!=', 'origin')->orderBy('name', 'ASC')->get();
        return view('portals.user.solutions.create', compact('modules'));
    }

    public function get_brands(Request $request)
    {
        $rules = [
            'module_uuid' => 'required'
        ];
        $this->validate($request, $rules);
        try {
            $ecu_files = ECUFile::whereRelation('ecu_file_records', 'module_uuid', $request->module_uuid)
                ->get(['uuid', 'ecu_uuid'])->pluck('ecu_uuid')->toArray();

            $brands = Brand::with([
                'ecus' => function ($query) use ($ecu_files) {
                    $query->whereIn('uuid', $ecu_files);
                }
            ])->whereHas('ecus', function ($query) use ($ecu_files) {
                $query->whereIn('uuid', $ecu_files);
            })->orderBy('name', 'ASC')->get();

            return response()->json([
                'status' => true,
                'message' => 'Brands Loaded Successfully',
                'data' => $brands,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function find_solution(Request $request)
    {
        $rules = [
            'module_uuid' => 'required',
            'brand_uuid' => 'required',
            'ecu_uuid' => 'required',
            'file' => 'required',
        ];
        $this->validate($request, $rules);
        $user = User::where('id', auth()->user()->id)->first();
        $file_records = [];
        $target_records = '';
        $target_files_content = [];
        $target_file_same_fix_type_conten = '';
        $result = '';
        $brand_uuid = $request->brand_uuid;
        $brand = Brand::find($request->brand_uuid);
        $fix_type = $request->module_uuid;
        $module = Module::where('uuid', $fix_type)->first();
        $is_free = $module->is_free;
        $price = $module->price;
        $user_credit = $user->balance;
        $user_file = $request->file;
        $user_file_content = file_get_contents($user_file);
        $origi_file_content = '';
        $origin_module_uuid = Module::where('name', 'Origin')->first();
        if ($user_credit >= $price or $is_free) {

            $ecu_uuid_re = $request->ecu_uuid;
            logger("ecu_uuid_re : " . $ecu_uuid_re);
            $user_file_name = $user_file->getClientOriginalName();
            $ecu_check = '';
            $file_check = '';
            try {
                $u_f_n = explode('_', $user_file_name);
                $u_f_n_1 = explode('--', $user_file_name);
                $u_f_n_ecu_name = @$u_f_n[1];
                $u_f_n_file_uuid = @$u_f_n_1[1];
                $ecu_check = ECU::where('name', $u_f_n_ecu_name)->first();
                $file_check = ECUFile::find($u_f_n_file_uuid);
            } finally {
            }
            if ($ecu_check && $file_check) {
                $checked_file_records = ECUFileRecord::where('ecu_file_uuid', $file_check->uuid)->get();
                foreach ($checked_file_records as $c_f_r) {
                    $c_f_r_content = file_get_contents($c_f_r->file);
                    if ($c_f_r->module_uuid == $fix_type) {
                        $target_file_same_fix_type_conten = $c_f_r_content;
                    } elseif ($c_f_r->module_uuid == $origin_module_uuid->uuid) {
                        $origi_file_content = $c_f_r_content;
                    } else {
                        array_push($target_files_content, $c_f_r_content);
                    }
                }

                if (strlen($user_file_content) == strlen($origi_file_content)) {
                    $fix = $target_file_same_fix_type_conten;
                    // $file0 = @$target_files_content[0];
                    // $file1 = @$target_files_content[1];
                    // $file2 = @$target_files_content[2];
                    $file_user = $user_file_content;
                    $origin_file = $origi_file_content;
                    $map = array();
                    $map1 = array();
                    for ($i = 0; $i < strlen($fix); $i++) {
                        if ($fix[$i] != $origin_file[$i]) {
                            $map[$i] = $fix[$i];
                        } elseif ($file_user[$i] != $origin_file[$i]) {
                            $map1[$i] = $file_user[$i];
                        }
                    }
                    for ($i = 0; $i < strlen($file_user); $i++) {
                        if (!empty($map[$i]) && empty($map1[$i])) {
                            $result .= $map[$i];
                        } elseif (empty($map[$i]) && !empty($map1[$i])) {
                            $result .= $map1[$i];
                        } elseif (empty($map[$i]) && empty($map1[$i])) {
                            $result .= $origin_file[$i];
                        } elseif (!empty($map[$i]) && !empty($map1[$i])) {
                            $result .= $map[$i];
                        }
                    }

                    $file_name = 'MagicSolution--' . $u_f_n_file_uuid . '--(' . $brand->name . '_' . $u_f_n_ecu_name . '_' . $module->name . '(No--CHK)' . '.bin';
                    Storage::disk('s3')->put('/fixed/' . $file_name, $result, 'public');
                    $target_files_content = [];
                    $path = 'https://carfix22.s3-eu-west-1.amazonaws.com/fixed/' . $file_name;
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'We can not find solution for your file you can Request a Solution .',
                    ]);
                }
                if ($path) {

                    $user->update([
                        'balance' => floatval($user->balance) - floatval($price)
                    ]);

                    return response()->json([
                        'status' => true,
                        'message' => 'You solution will be downloaded.',
                        'data' => [
                            'url' => $path,
                            'filename' => $file_name,
                        ],
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'We can not find solution for your file.',
                    ]);
                }
            } else {
                try {
                    $ecu = ECU::where('uuid', $ecu_uuid_re)->first();
                    //$ecu_recordes_uuid=ECUFile::where('ecu_uuid', $ecu_uuid_re)->get();

                    $ecu_files = ECUFile::where('ecu_uuid', $ecu_uuid_re)->get();


                    //count[{"id":1,"uuid":"b541b4cd-1a1a-4dd5-8f1d-4b5e1f07310d","ecu_uuid":"7673557c-6759-451a-9168-e04b9397a9d6","ecu_name":"EDC17C57"},{"id":5,"uuid":"27749fb6-b805-47bd-b66d-f410ccb0008a","ecu_uuid":"7673557c-6759-451a-9168-e04b9397a9d6","ecu_name":"EDC17C57"}]
                    //logger("ecu_files".''.$ecu_files);
                    logger("ecu_files count" . count($ecu_files));
                    //$ecu_recordes_uuid=ECUFileRecord::where('ecu_file_uuid', $ecu_files[0]->uuid)->get();
                    //logger("ecu_recordes_uuid " . $ecu_recordes_uuid);
                    $target_record_uuid = '';
                    for ($i = 0; $i < count($ecu_files); $i++) {
                        $ecu_recordes_uuid = ECUFileRecord::where('ecu_file_uuid', $ecu_files[$i]->uuid)->get();
                        //dd($ecu_recordes_uuid);
                        logger("ecu_recordes_uuid" . count($ecu_recordes_uuid));
                        for ($j = 0; $j < count($ecu_recordes_uuid); $j++) {
                            $test_file = file_get_contents($ecu_recordes_uuid[$j]->file);
                            logger("test_file -" . $j . '-' . strlen($test_file));
                            if ($user_file_content == $test_file) {
                                $target_record_uuid .= $ecu_recordes_uuid[$j]->ecu_file_uuid;
                                break;
                            }
                        }
                    }

                    $records = ECUFileRecord::where('ecu_file_uuid', $target_record_uuid)->get();
                    logger("Records count " . count($records));
                    if (count($records) <= 0) {
                        return response()->json([
                            'status' => false,
                            'message' => 'We can not find solution for your file , you can request soulution by click over + icon',
                        ]);
                    }
                    foreach ($records as $target) {
                        $target_content = file_get_contents($target->file);
                        if ($target->module_uuid == $fix_type) {
                            $target_file_same_fix_type_conten = $target_content;
                        } elseif ($target->module_uuid == $origin_module_uuid->uuid) {
                            $origi_file_content = $target_content;
                        } else {
                            array_push($target_files_content, $target_content);
                        }
                    }

                    if ($user_file_content === $origi_file_content) {
                        $result .= $target_file_same_fix_type_conten;
                    } else {
                        $fix = $target_file_same_fix_type_conten;
                        // $file0 = @$target_files_content[0];
                        // $file1 = @$target_files_content[1];
                        // $file2 = @$target_files_content[2];
                        $file_user = $user_file_content;
                        $origin_file_new = $origi_file_content;
                        $map = array();
                        $map1 = array();
                        for ($i = 0; $i < strlen($fix); $i++) {
                            if ($fix[$i] != $origin_file_new[$i]) {
                                $map[$i] = $fix[$i];
                            } elseif ($file_user[$i] != $origin_file_new[$i]) {
                                $map1[$i] = $file_user[$i];
                            }
                        }
                        for ($i = 0; $i < strlen($file_user); $i++) {
                            if (!empty($map[$i]) && empty($map1[$i])) {
                                $result .= $map[$i];
                            } elseif (empty($map[$i]) && !empty($map1[$i])) {
                                $result .= $map1[$i];
                            } elseif (empty($map[$i]) && empty($map1[$i])) {
                                $result .= $origin_file_new[$i];
                            } elseif (!empty($map[$i]) && !empty($map1[$i])) {
                                $result .= $map[$i];
                            }
                        }
                    }

                    $file_name = 'MagicSolution--' . $target_record_uuid . '--(' . $brand->name . '_' . $ecu->name . '_' . $module->name . '(No--CHK)' . '.bin';
                    Storage::disk('s3')->put('/fixed/' . $file_name, $result, 'public');
                    $target_files_content = [];
                    $path = 'https://carfix22.s3-eu-west-1.amazonaws.com/fixed/' . $file_name;

                    if ($path) {

                        $user->update([
                            'balance' => floatval($user->balance) - floatval($price)
                        ]);

                        return response()->json([
                            'status' => true,
                            'message' => 'Done successfully',
                            'data' => [
                                'url' => $path,
                                'filename' => $file_name,
                            ],
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'We can not find solution for your file , if you need it , craete a Solution request by click over + icon',
                        ]);
                    }
                } catch (\Exception $ex) {
                    return response()->json([
                        'status' => false,
                        'message' => $ex->getMessage() . 'AASA',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'You do not have any Balance ',
            ]);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'broken_file' => 'required',
            'module_uuid' => 'required',
            'ecu_uuid' => 'required',
        ];
        $this->validate($request, $rules);

        $ecu_files = ECUFile::query()->where('ecu_uuid', $request->ecu_uuid)->get();

        if ($request->hasFile('broken_file')) {
            $broken_file = Storage::disk('s3')->putFile('/broken', $request->file('broken_file'), 'public');
        }
        $broken_file_md5 = md5(file_get_contents('https://carfix22.s3-eu-west-1.amazonaws.com/' . $broken_file));
        $ecu_file_uuid = null;
        foreach ($ecu_files as $ecu_file) {
            $path = 'https://carfix22.s3-eu-west-1.amazonaws.com/';
            $origin_file_md5 = md5(file_get_contents($path . urlencode($ecu_file->getRawOriginal()['origin_file'])));
            if ($broken_file_md5 == $origin_file_md5) {
                $ecu_file_uuid = $ecu_file->uuid;
                break;
            }
        }
        if (is_null($ecu_file_uuid)) {
            return response()->json(['message' => "The given data was invalid.", 'errors' => ['broken_files' => ['The provided file is invalid.']]], 422);
        }

        $ecu = ECU::query()->find($request->ecu_uuid);
        $ecu_file = ECUFile::query()->find($ecu_file_uuid);
        $brand = Brand::query()->find($ecu->brand_uuid);
        $data = $request->only(['broken_file', 'module_uuid', 'ecu_uuid']);
        $data['ecu_file_uuid'] = $ecu_file_uuid;
        $data['broken_file'] = $broken_file;
        $data['brand_uuid'] = $ecu->brand_uuid;
        $data['fixed_file'] = $ecu_file->getRawOriginal()['fixed_file'];
        $data['ownerable_uuid'] = auth()->user()->uuid;
        $data['ownerable_type'] = User::class;
        $solution = Solution::query()->create($data);
        $module = Module::query()->find($request->module_uuid);
        if (!$module->is_free) {
            $user = User::query()->find(auth()->user()->uuid);
            $user->update(['balance' => $user->balance - $module->price]);
        }
        $file_name = $request->file('broken_file')->getClientOriginalName();
        $file_size = round($request->file('broken_file')->getSize() / 1000 / 1000, 2);
        if ($request->ajax()) {
            return response()->json(['status' => true, 'url' => $solution->fixed_file, 'brand_name' => $brand->name, 'module_name' => $module->name, 'ecu_name' => $ecu->name, 'file_name' => $file_name, 'file_size' => $file_size]);
        }
        Session::flash('success_message', __('item_added'));
        return redirect()->back();
    }

    public function update(Solution $solution, Request $request)
    {
        $rules = [
            'broken_file' => 'nullable',
            'module_uuid' => 'required',
            'ecu_uuid' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['broken_file', 'module_uuid', 'brand_uuid', 'ecu_uuid']);
        $ecu = ECU::query()->find($request->ecu_uuid);
        if ($request->hasFile('broken_file')) {
            $broken_file = Storage::disk('s3')->putFile('/broken', $request->file('broken_file'), 'public');
            //            $broken_file = $request->broken_file('broken_file')->store('public');
            $data['broken_file'] = $broken_file;
        }
        $data['brand_uuid'] = $ecu->brand_uuid;
        $data['fixed_file'] = $ecu->file;
        $solution->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function destroy($uuid, Request $request)
    {
        Solution::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $solutions = Solution::query()->orderByDesc('id')->where('ownerable_uuid', auth()->id())->where('ownerable_type', User::class);
        return Datatables::of($solutions)
            ->filter(function ($query) use ($request) {
                if ($request->get('manufacturer_uuid')) {
                    $query->where('manufacturer_uuid', $request->manufacturer_uuid);
                }
                if ($request->get('car_model_uuid')) {
                    $query->where('car_model_uuid', $request->car_model_uuid);
                }
                if ($request->get('category_uuid')) {
                    $query->where('category_uuid', $request->category_uuid);
                }
            })->addColumn('action', function ($solution) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $solution->uuid . '" ';
                $data_attr .= 'data-module_uuid="' . $solution->module_uuid . '" ';
                $data_attr .= 'data-brand_uuid="' . $solution->brand_uuid . '" ';
                $data_attr .= 'data-ecu_uuid="' . $solution->ecu_uuid . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $solution->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }
}
