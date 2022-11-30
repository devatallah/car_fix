<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{

    public function index(Request $request)
    {
        return view('portals.admin.brands.index');

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

    public function destroy($uuid, Request $request)
    {
        Brand::query()->whereIn('uuid', explode(',', $uuid))->delete();
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
