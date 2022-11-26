<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarModel;
use App\Models\Manufacturer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class CarModelController extends Controller
{
    public function index()
    {
        $manufacturers = Manufacturer::all();
        return view('portals.admin.car_models.index', compact('manufacturers'));
    }

    public function store(Request $request)
    {
        $rules = [
            'manufacturer_uuid' => 'required|exists:manufacturers,uuid',
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only('manufacturer_uuid', 'name');
        CarModel::query()->create($data);
        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));
        return redirect('admin/car_models');
    }

    public function update($id, Request $request)
    {
        $car_model = CarModel::query()->find($id);
        $rules = [
            'manufacturer_uuid' => 'required|exists:manufacturers,uuid',
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only('manufacturer_uuid', 'name');
        $car_model->update($data);
        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));
        return redirect()->back();
    }

    public function destroy($uuid)
    {
        try {
            CarModel::query()->whereIn('uuid', explode(',', $uuid))->delete();
        } catch (Exception $e) {
            return response()->json(['status' => false]);
        }
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $car_models = CarModel::query();
        return Datatables::of($car_models)
            ->filter(function ($query) use ($request) {
                if ($request->name) {
                    $query->where("name", 'Like', "%" . $request->name . "%");
                }
                if ($request->manufacturer_uuid) {
                    $query->where('manufacturer_uuid', $request->manufacturer_uuid);
                }
            })->addColumn('action', function ($car_model) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $car_model->uuid . '" ';
                $data_attr .= 'data-manufacturer_uuid="' . $car_model->manufacturer_uuid . '" ';
                $data_attr .= 'data-name="' . $car_model->name . '" ';

                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $car_model->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }

}
