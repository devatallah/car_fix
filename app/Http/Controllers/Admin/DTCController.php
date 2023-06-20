<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\DTC;
use App\Models\ECU;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class DTCController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::get();
        $ecus = ECU::get();

        return view('portals.admin.dtcs.index', compact("ecus", "brands"));
    }

    public function getBrands(Request $request)
    {

        if ($request->get('brand_uuid')) {
            $brand = $request->brand_uuid;
            $ecus = ECU::where('brand_uuid', $brand)->latest()->get();
        } else {
            $ecus = "";
        }

        return response()->json([
            'status' => true,
            'data' => $ecus
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'name' => 'required',
            'ecu_uuid' => 'required|exists:ecus,uuid',
            'brand_uuid' => 'required|exists:brands,uuid',
        ];
        $this->validate($request, $rules);

        $data = $request->only(['name', 'ecu_uuid', 'brand_uuid']);

        DTC::query()->create($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('dtcs');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DTC $dtc
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DTC $dtc)
    {
        $rules = [
            'name' => 'required',
            'ecu_uuid' => 'required|exists:ecus,uuid',
            'brand_uuid' => 'required|exists:brands,uuid',
        ];
        $this->validate($request, $rules);

        $data = $request->only(['name', 'ecu_uuid', 'brand_uuid']);

        $dtc->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect('dtcs');
    }

    public function destroy($uuid)
    {
        $dtc = DTC::query()->whereIn('uuid', explode(',', $uuid))->first();
        $dtc->files()->delete();
        $dtc->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $dtcs = DTC::query()->orderByDesc('id');
        return DataTables::of($dtcs)
            ->filter(function ($query) use ($request) {
                if ($request->get('ecu_uuid')) {
                    $query->where('ecu_uuid', $request->ecu_uuid);
                }
                if ($request->get('brand_uuid')) {
                    $query->where('brand_uuid', $request->brand_uuid);
                }
            })->addColumn('action', function ($dtc) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $dtc->uuid . '" ';
                $data_attr .= 'data-ecu_uuid="' . $dtc->ecu_uuid . '" ';
                $data_attr .= 'data-brand_uuid="' . $dtc->brand_uuid . '" ';
                $data_attr .= 'data-name="' . $dtc->name . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <a href="' . url("admin/dtc_files?dtc_uuid=$dtc->uuid") . '" class="btn btn-sm btn-outline-primary">Files</a>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $dtc->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }
}