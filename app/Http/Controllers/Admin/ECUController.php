<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\ECU;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ECUController extends Controller
{

    public function index(Request $request)
    {
        $modules = Module::all();
        $brands = Brand::all();
        return view('portals.admin.ecus.index', compact('modules', 'brands'));

    }

    public function store(Request $request)
    {
        $rules = [
            'file' => 'required|file',
            'module_uuid' => 'required',
            'brand_uuid' => 'required',
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['name', 'file', 'module_uuid', 'brand_uuid']);
        $module = Module::query()->find($request->module_uuid);
        $brand = Brand::query()->find($request->brand_uuid);
        if ($request->hasFile('file')) {
            $file_name = Storage::disk('s3')->putFileAs('',$request->file('file'),
                'fixed/magicModule ('.$brand->name.' '.$request->name.' ('.$module->name.') (NoChk).'.$request->file('file')->extension(), ['visibility' => 'public']);
//            $file = $request->file('file')->store('public');
            $data['file'] = $file_name;
        }
        ECU::query()->create($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('ecus');
    }

    public function update(ECU $ecu, Request $request)
    {
        $rules = [
            'file' => 'nullable|file',
            'module_uuid' => 'required',
            'brand_uuid' => 'required',
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['name', 'file', 'module_uuid', 'brand_uuid']);
        $module = Module::query()->find($request->module_uuid);
        $brand = Brand::query()->find($request->brand_uuid);
        if ($request->hasFile('file')) {
            $file_name = Storage::disk('s3')->putFileAs('',$request->file('file'),
                'fixed/magicModule ('.$brand->name.' '.$request->name.' ('.$module->name.') (NoChk).'.$request->file('file')->extension(), ['visibility' => 'public']);
            $data['file'] = $file_name;
        }
        $ecu->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function destroy($uuid, Request $request)
    {
        ECU::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $ecus = ECU::query()->orderByDesc('id');
        return Datatables::of($ecus)
            ->filter(function ($query) use ($request) {
                if ($request->name) {
                    $query->where("name", 'Like', "%" . $request->name . "%");
                }
                if ($request->get('brand_uuid')) {
                    $query->where('brand_uuid', $request->brand_uuid);
                }
                if ($request->get('car_model_uuid')) {
                    $query->where('car_model_uuid', $request->car_model_uuid);
                }
                if ($request->get('module_uuid')) {
                    $query->where('module_uuid', $request->module_uuid);
                }
            })->addColumn('action', function ($ecu) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $ecu->uuid . '" ';
                $data_attr .= 'data-module_uuid="' . $ecu->module_uuid . '" ';
                $data_attr .= 'data-brand_uuid="' . $ecu->brand_uuid . '" ';
                $data_attr .= 'data-file="' . $ecu->file . '" ';
                $data_attr .= 'data-name="' . $ecu->name . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $ecu->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }

}
