<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ECUFile;
use App\Models\ECUFileRecord;
use App\Models\Module;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ECUFileRecordsController extends Controller
{

    public function index(Request $request)
    {
        $ecu_file = ECUFile::find($request->file_uuid);
        if (!$ecu_file) {
            return redirect('admin/ecus');
        }
        $modules = Module::all();
        return view('portals.admin.ecu_file_records.index', compact('modules', 'ecu_file'));
    }

    public function store(Request $request)
    {
        $rules = [
            'ecu_file_uuid' => 'required',
            'record.*.file' =>  [
                'required', function ($input, $value) {
                    return $value->getClientOriginalExtension() == 'bin';
                }
            ],
            'record.*.module_uuid' => 'required',
        ];
        $this->validate($request, $rules);

        $time_stamp = Carbon::now()->timestamp;

        DB::beginTransaction();
        $ecu_file = ECUFile::where('uuid', $request->ecu_file_uuid)->first();
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
            return response()->json(['status' => false, 'message' => 'File not exist try again']);
        }
    }

    public function update(Request $request, $uuid)
    {
        $rules = [
            'module_uuid' => 'required',
        ];
        $this->validate($request, $rules);

        $record = ECUFileRecord::where('uuid', $uuid)->first();
        if (!$record->exists()) {
            return response()->json(['status' => false, 'message' => 'File record not exist']);
        }

        $data = $request->only(['file', 'module_uuid']);
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
        $record->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function destroy($uuid)
    {
        ECUFileRecord::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request, $ecu_file_uuid)
    {
        $ecu_files_records = ECUFileRecord::where('ecu_file_uuid', $ecu_file_uuid)->orderByDesc('id')->get();

        return DataTables::of($ecu_files_records)
            ->filter(function ($query) use ($request) {
                if ($request->get('module_uuid')) {
                    $query->where('module_uuid', $request->module_uuid);
                }
            })->addColumn('action', function ($record) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $record->uuid . '" ';
                $data_attr .= 'data-module_uuid="' . $record->module_uuid . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $record->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }
}