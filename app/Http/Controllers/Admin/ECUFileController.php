<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ECUFile;
use App\Models\Module;
use App\Models\ECU;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        return view('portals.admin.ecu_files.index', compact('ecu'));

    }

    public function store(Request $request)
    {
        $rules = [
            'fixed_file' => 'required',
            'origin_file' => 'required',
            'ecu_uuid' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['fixed_file', 'origin_file', 'ecu_uuid']);
        $ecu = ECU::query()->find($request->ecu_uuid);
        $module = Module::query()->find($ecu->module_uuid);
        $brand = Brand::query()->find($ecu->brand_uuid);
        $time_stamp = Carbon::now()->timestamp;
        if ($request->hasFile('fixed_file')) {
            $fixed_file = Storage::disk('s3')->putFileAs('',$request->file('fixed_file'),
                'fixed/magicSolution ('.$brand->name.' '.$module->name.') (NoChk) '. $time_stamp .'.'.$request->file('fixed_file')->extension(), ['visibility' => 'public']);
            $data['fixed_file'] = $fixed_file;
        }
        if ($request->hasFile('origin_file')) {
            $origin_file = Storage::disk('s3')->putFileAs('',$request->file('origin_file'),
                'origin/magicSolution ('.$brand->name.' '.$module->name.') (NoChk) '. $time_stamp .'.'.$request->file('origin_file')->extension(), ['visibility' => 'public']);
            $data['origin_file'] = $origin_file;
        }
        ECUFile::query()->create($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('ecus');
    }

    public function update(ECUFile $ecu_file, Request $request)
    {
        $rules = [
            'fixed_file' => 'required',
            'origin_file' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['fixed_file', 'origin_file']);
        $ecu = ECU::query()->find($ecu_file->ecu_uuid);
        $module = Module::query()->find($ecu->module_uuid);
        $brand = Brand::query()->find($ecu->brand_uuid);
        $time_stamp = Carbon::now()->timestamp;
        if ($request->hasFile('fixed_file')) {
            $fixed_file = Storage::disk('s3')->putFileAs('',$request->file('fixed_file'),
                'fixed/magicSolution ('.$brand->name.' '.$module->name.') (NoChk) '. $time_stamp .'.'.$request->file('fixed_file')->extension(), ['visibility' => 'public']);
            $data['fixed_file'] = $fixed_file;
        }
        if ($request->hasFile('origin_file')) {
            $origin_file = Storage::disk('s3')->putFileAs('',$request->file('origin_file'),
                'origin/magicSolution ('.$brand->name.' '.$module->name.') (NoChk) '. $time_stamp .'.'.$request->file('origin_file')->extension(), ['visibility' => 'public']);
            $data['origin_file'] = $origin_file;
        }
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

    public function indexTable(Request $request)
    {
        $ecu_files = ECUFile::query()->where('ecu_uuid', $request->ecu_uuid)->orderByDesc('id');
        return Datatables::of($ecu_files)
            ->filter(function ($query) use ($request) {
            })->addColumn('action', function ($ecu_file) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $ecu_file->uuid . '" ';
                $data_attr .= 'data-fixed_file="' . $ecu_file->module_uuid . '" ';
                $data_attr .= 'data-origin_file="' . $ecu_file->file . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $ecu_file->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }

}
