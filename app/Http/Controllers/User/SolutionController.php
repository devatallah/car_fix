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

class SolutionController extends Controller
{

    public function index(Request $request)
    {
        $modules = Module::where('name', '!=', 'origin')->get();
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

            $brands = Brand::with(['ecus' => function ($query) use ($ecu_files) {
                $query->whereIn('uuid', $ecu_files);
            }])->whereHas('ecus', function ($query) use ($ecu_files) {
                $query->whereIn('uuid', $ecu_files);
            })->get();

            return response()->json([
                'status' => true,
                'message' => 'Brands Loaded Successfully',
                'data' => $brands,
            ]);
        } catch (\Exception  $e) {
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

        $fix_type = $request->module_uuid;
        $module = Module::where('uuid', $fix_type)->first();
        $user_file = $request->file;

        $user_file_content = file_get_contents($user_file);

        try {

            $ecu = ECU::where('brand_uuid', $request->brand_uuid)->first();

            $ecu_files = ECUFile::where('ecu_uuid', $ecu->uuid)->get();

            $target_file_uuid = '';
            $target_records_content_not_same_user_fix_type = [];
            $target_record_content_with_same_user_fix_type = '';
            $result = '';

            foreach ($ecu_files as $file) {
                $file_records = $file->ecu_file_records;

                foreach ($file_records as $record) {
                    $record_content = file_get_contents($record->file);

                    // get file size need asynch
                    // file_exists($record->file) && filesize($record->file)
                    // filesize($user_file) === filesize($record->file)

                    if ($user_file_content === $record_content) {
                        $target_file_uuid .= $file->uuid;

                        break 2;
                    }
                }
            }

            $records = ECUFileRecord::where('ecu_file_uuid', $target_file_uuid)->get();
            dd($fix_type , $records[0]->module_uuid,count($records));

            // search on other records on same file
            foreach ($records as $record) {
                $record_content .= file_get_contents($record->file);
                if ($record->module_uuid == $fix_type) {
                    $target_record_content_with_same_user_fix_type .= $record_content;
                } else {
                    array_push($target_records_content_not_same_user_fix_type, $record_content);
                }
            }

            // ************ we need to fix target_records_content_not_same_user_fix_type loop ************
            // for ($i = 0; $i < count($target_records_content_not_same_user_fix_type); $i++) {
                dd(strlen($target_record_content_with_same_user_fix_type));
            for ($j = 0; $j < strlen($target_record_content_with_same_user_fix_type); $j++) {
                if ($target_record_content_with_same_user_fix_type[$j] != $target_records_content_not_same_user_fix_type[0][$j] && $target_record_content_with_same_user_fix_type[$j] != $user_file_content[$j] && $target_record_content_with_same_user_fix_type[$j] != $target_records_content_not_same_user_fix_type[1][$j] && $target_record_content_with_same_user_fix_type[$j] != $target_records_content_not_same_user_fix_type[2][$j]) {
                    $result .= $target_record_content_with_same_user_fix_type[$j];
                    dd($result);

                } else {
                    $result .= $user_file_content[$j];
                    dd($result);

                }
            }
            // }
            $file_name = 'magicSolution_' . $ecu->name . '_' . $module->name . '.bin';
            $file_name = str_replace(' ', '', $file_name);
            $myfile = fopen($file_name, "w");
            fwrite($myfile, $result);
            fclose($myfile);

            // reset target_file records
            $target_file_uuid = '';
            $target_records_content_not_same_user_fix_type = [];
            $target_record_content_with_same_user_fix_type = '';
            $result = '';

            $path = asset($file_name);

            if ($path) {
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
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage(),
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
