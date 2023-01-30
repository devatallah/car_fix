<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\ECU;
use App\Models\Brand;
use App\Models\ECUFileRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ECUController extends Controller
{

    public function index(Request $request)
    {
        $brands = Brand::all();
        return view('portals.admin.ecus.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $rules = [
            'brand_uuid' => 'required',
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['name', 'brand_uuid']);
        $brand = Brand::query()->find($request->brand_uuid);
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
            'brand_uuid' => 'required',
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['name', 'brand_uuid']);
        $brand = Brand::query()->find($request->brand_uuid);
        $ecu->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function destroy($uuid, Request $request)
    {
        $ecu = ECU::query()->whereIn('uuid', explode(',', $uuid))->first();
        $files = $ecu->files;
        $files->each(function ($item) {
            $item->ecu_file_records()->delete();
        });
        $ecu->files()->delete();
        $ecu->delete();
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
            })->addColumn('action', function ($ecu) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $ecu->uuid . '" ';
                $data_attr .= 'data-brand_uuid="' . $ecu->brand_uuid . '" ';
                $data_attr .= 'data-name="' . $ecu->name . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <a href="' . url("admin/ecu_files?ecu_uuid=$ecu->uuid") . '" class="btn btn-sm btn-outline-primary">Files</a>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $ecu->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }
}