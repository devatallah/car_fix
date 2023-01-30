<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\ECUFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{

    public function index(Request $request)
    {
        return view('portals.admin.brands.index');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only('name');

        Brand::query()->create($data);


        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('brands');
    }

    public function update(Brand $brand, Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only('name');
        $brand->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function destroy($uuid, Request $request)
    {
        $brand = Brand::query()->whereIn('uuid', explode(',', $uuid))->first();
        $ecus = $brand->ecus;
        $ecus_ids = $brand->ecus->pluck('uuid')->toArray();
        ECUFile::whereIn('ecu_uuid', $ecus_ids)->get()->each(function ($item) {
            $item->ecu_file_records()->delete();
        });
        $ecus->each(function ($item) {
            $item->files()->delete();
        });
        $brand->ecus()->delete();
        $brand->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $brands = Brand::query()->orderByDesc('id');
        return Datatables::of($brands)
            ->filter(function ($query) use ($request) {
                if ($request->name) {
                    $query->where("name", 'Like', "%" . $request->name . "%");
                }
            })->addColumn('action', function ($brand) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $brand->uuid . '" ';
                $data_attr .= 'data-name="' . $brand->name . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $brand->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }
}