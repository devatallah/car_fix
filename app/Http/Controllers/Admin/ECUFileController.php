<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ECUFile;
use App\Models\ECU;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class ECUFileController extends Controller
{

    public function index(Request $request)
    {
        $ecu = ECU::query()->withCount('files')->find($request->ecu_uuid);
        $ecus = ECU::all();
        if (!$ecu) {
            return redirect('admin/ecus');
        }
        return view('portals.admin.ecu_files.index', compact('ecu', 'ecus'));
    }

    public function store(Request $request)
    {
        $rules = [
            'ecu_uuid' => 'required',
        ];
        $this->validate($request, $rules);

        $data_file = $request->only(['ecu_uuid']);
        ECUFile::create($data_file);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));
        return redirect('ecus');
    }

    public function update($uuid, Request $request)
    {
        $rules = [
            'ecu_uuid' => 'required',
        ];
        $this->validate($request, $rules);

        $ecu_file = ECUFile::find($uuid);

        $data_file = $request->only(['ecu_uuid']);
        $ecu_file->update($data_file);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));
        return redirect("/admin/ecus");
    }

    public function destroy($uuid)
    {
        $file = ECUFile::query()->whereIn('uuid', explode(',', $uuid))->first();
        $file->ecu_file_records()->delete();
        $file->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable($ecu_uuid)
    {
        $ecu_files = ECUFile::where('ecu_uuid', $ecu_uuid)->get();

        return Datatables::of($ecu_files)
            ->addColumn('action', function ($ecu_file) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $ecu_file->uuid . '" ';
                $data_attr .= 'data-ecu_uuid="' . $ecu_file->ecu_uuid . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <a href="' . url("admin/ecu_file_records?file_uuid=$ecu_file->uuid") . '" class="btn btn-sm btn-outline-primary">Records</a>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $ecu_file->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }
}