<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class ManufacturerController extends Controller
{

    public function index(Request $request)
    {
        return view('portals.admin.manufacturers.index');

    }

    public function update(Manufacturer $manufacturer, Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only('name');
        $manufacturer->update($data);

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

        Manufacturer::query()->create($data);


        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('manufacturers');
    }

    public function destroy($uuid, Request $request)
    {
        $manufacturers = Manufacturer::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $manufacturers = Manufacturer::query()->orderByDesc('id');
        return Datatables::of($manufacturers)
            ->filter(function ($query) use ($request) {
                if ($request->name) {
                    $query->where("name", 'Like', "%" . $request->name . "%");
                }
            })->addColumn('action', function ($manufacturer) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $manufacturer->uuid . '" ';
                $data_attr .= 'data-image="' . $manufacturer->image . '" ';
                $data_attr .= 'data-name="' . $manufacturer->name . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $manufacturer->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }

}
