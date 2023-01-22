<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ECUFile;
use App\Models\Module;
use App\Models\ECU;
use App\Models\Brand;
use App\Models\ECUFileRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ECUFileController extends Controller
{

    public function index(Request $request)
    {
        $ecu = ECU::query()->find($request->ecu_uuid);
        if (!$ecu) {
            return redirect('admin/ecus');
        }
        $modules = Module::all();
        return view('portals.admin.ecu_files.index', compact('modules', 'ecu'));
    }

    public function store(Request $request)
    {
        $rules = [
            'ecu_uuid' => 'required',
            'record.*.file' =>  [
                'required', function ($input, $value) {
                    return $value->getClientOriginalExtension() == 'bin';
                }
            ],
            'record.*.module_uuid' => 'required',
        ];
        $this->validate($request, $rules);
        $data_file = $request->only(['ecu_uuid']);
        $time_stamp = Carbon::now()->timestamp;

        DB::beginTransaction();

        $ecu_file = ECUFile::query()->create($data_file);

        if ($ecu_file) {

            foreach ($request->record as $key => $value) {

                $item = $value;

                $fix_type = $item['module_uuid'];

                $record_file = $item['file'];

                $module = Module::query()->find($fix_type);

                $file = Storage::disk('s3')->putFileAs(
                    '',
                    $record_file,
                    'ecus/file/' . $module->id . '_' . $time_stamp . '.bin',
                    ['visibility' => 'public']
                );
                //$record_file->getClientOriginalExtension()

                $record = new ECUFileRecord();
                $record->ecu_file_uuid = $ecu_file->uuid;
                $record->module_uuid = $fix_type;
                $record->file = $file;
                $record->save();

                if (!$record) {
                    DB::rollBack();
                    $err =  "Create File Record Error";
                    return response()->json(['status' => false, 'errors' => 'Create File Record Error']);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['status' => true]);
            }
            Session::flash('success_message', __('item_added'));

            return redirect('ecus');
        } else {
            DB::rollBack();
            $err =  "Create File Record Error";
            return response()->json(['status' => false]);
        }
    }

    public function update(ECUFile $ecu_file, Request $request)
    {
        $rules = [
            // 'fixed_file' => 'required',
            // 'origin_file' => 'required',
            'file' => 'required',
            'module_uuid' => 'required',
        ];
        $this->validate($request, $rules);
        // $data = $request->only(['fixed_file', 'origin_file']);
        $data = $request->only(['file', 'module_uuid']);

        // $ecu = ECU::query()->find($ecu_file->ecu_uuid);
        // $module = Module::query()->find($ecu->module_uuid);
        // $brand = Brand::query()->find($ecu->brand_uuid);
        $module = Module::query()->find($request->module_uuid);

        $time_stamp = Carbon::now()->timestamp;
        if ($request->hasFile('file')) {
            $file = Storage::disk('s3')->putFileAs(
                '',
                $request->file('file'),
                'ecus/file (' . $module->name . ') (NoChk) ' . $time_stamp . '.' . $request->file('file')->extension(),
                ['visibility' => 'public']
            );
            $data['file'] = $file;
        }
        // if ($request->hasFile('fixed_file')) {
        //     $fixed_file = Storage::disk('s3')->putFileAs(
        //         '',
        //         $request->file('fixed_file'),
        //         'fixed/magicSolution (' . $brand->name . ' ' . $module->name . ') (NoChk) ' . $time_stamp . '.' . $request->file('fixed_file')->extension(),
        //         ['visibility' => 'public']
        //     );
        //     $data['fixed_file'] = $fixed_file;
        // }
        // if ($request->hasFile('origin_file')) {
        //     $origin_file = Storage::disk('s3')->putFileAs(
        //         '',
        //         $request->file('origin_file'),
        //         'origin/magicSolution (' . $brand->name . ' ' . $module->name . ') (NoChk) ' . $time_stamp . '.' . $request->file('origin_file')->extension(),
        //         ['visibility' => 'public']
        //     );
        //     $data['origin_file'] = $origin_file;
        // }
        $ecu_file->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function destroy($uuid, Request $request)
    {
        ECUFile::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request, $ecu_uuid)
    {
        $ecu_files = ECUFile::where('ecu_uuid', $ecu_uuid)
            ->get(['uuid'])->pluck('uuid')->toArray();

        $ecu_files_records = ECUFileRecord::with('module')->whereIn('ecu_file_uuid', $ecu_files)->orderByDesc('id');

        // $ecu_files = ECUFile::with(['ecu_file_records'])->where('ecu_uuid', $request->ecu_uuid)->orderByDesc('id');
        // $ecu_files_records = ECUFileRecord::with('module')->orderByDesc('id'); //->where('ecu_uuid', $request->ecu_uuid)
        return Datatables::of($ecu_files_records)
            ->filter(function ($query) use ($request) {
                if ($request->get('module_uuid')) {
                    $query->where('module_uuid', $request->module_uuid);
                }
            })->addColumn('action', function ($ecu_files_records) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $ecu_files_records->uuid . '" ';
                // $data_attr .= 'data-file="' . $ecu_files_records->file . '" ';
                $data_attr .= 'data-module_uuid="' . $ecu_files_records->module_uuid . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $ecu_files_records->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }
}
