<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ECU;
use App\Models\ECUFile;
use App\Models\EcuSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class EcuSignatureController extends Controller
{
    public function index(Request $request)
    {
        $ecus = ECU::orderBy('name')->get();
        return view('portals.admin.ecu_signatures.index', compact('ecus'));
    }

    public function store(Request $request)
    {
        $rules = [
            'ecu_uuid'  => 'required|string',
            'file_size' => 'required|integer|min:1',
        ];
        $this->validate($request, $rules);

        $data = $request->only([
            'ecu_uuid', 'ecu_file_uuid', 'file_size',
            'signature_offset', 'signature_bytes',
            'car_make', 'car_model', 'year_range',
            'ecu_type', 'hw_sw_number', 'description',
        ]);

        EcuSignature::create($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));
        return redirect()->back();
    }

    public function update(Request $request, EcuSignature $ecu_signature)
    {
        $rules = [
            'ecu_uuid'  => 'required|string',
            'file_size' => 'required|integer|min:1',
        ];
        $this->validate($request, $rules);

        $data = $request->only([
            'ecu_uuid', 'ecu_file_uuid', 'file_size',
            'signature_offset', 'signature_bytes',
            'car_make', 'car_model', 'year_range',
            'ecu_type', 'hw_sw_number', 'description',
        ]);

        $ecu_signature->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));
        return redirect()->back();
    }

    public function destroy($uuid)
    {
        EcuSignature::whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $signatures = EcuSignature::with(['ecu.brand'])->orderByDesc('id');

        return DataTables::of($signatures)
            ->filter(function ($query) use ($request) {
                if ($request->ecu_uuid) {
                    $query->where('ecu_uuid', $request->ecu_uuid);
                }
            })
            ->addColumn('action', function ($sig) {
                $d  = 'data-uuid="' . $sig->uuid . '" ';
                $d .= 'data-ecu_uuid="' . $sig->ecu_uuid . '" ';
                $d .= 'data-ecu_file_uuid="' . $sig->ecu_file_uuid . '" ';
                $d .= 'data-file_size="' . $sig->file_size . '" ';
                $d .= 'data-signature_offset="' . $sig->signature_offset . '" ';
                $d .= 'data-signature_bytes="' . $sig->signature_bytes . '" ';
                $d .= 'data-car_make="' . $sig->car_make . '" ';
                $d .= 'data-car_model="' . $sig->car_model . '" ';
                $d .= 'data-year_range="' . $sig->year_range . '" ';
                $d .= 'data-ecu_type="' . $sig->ecu_type . '" ';
                $d .= 'data-hw_sw_number="' . $sig->hw_sw_number . '" ';
                $d .= 'data-description="' . $sig->description . '" ';

                $s  = '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#edit_modal" ' . $d . '>' . __('edit') . '</button>';
                $s .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $sig->uuid . '">' . __('delete') . '</button>';
                return $s;
            })
            ->make(true);
    }

    /**
     * AJAX: Get ECU files for a given ECU (for the create/edit modal dropdowns).
     * GET /admin/ecu_signatures/ecu-files?ecu_uuid=...
     */
    public function getEcuFiles(Request $request)
    {
        $files = ECUFile::where('ecu_uuid', $request->ecu_uuid)->get(['uuid', 'id']);
        return response()->json($files);
    }
}
