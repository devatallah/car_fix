<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ECURequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class ECURequestController extends Controller
{

    public function index(Request $request)
    {
        return view('portals.admin.ecu_requests.index');

    }

    public function update(ECURequest $ecu_request, Request $request)
    {
        $rules = [
            'ecu' => 'required|string|max:255',
            'module' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only('ecu', 'module', 'brand');
        $ecu_request->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $rules = [
            'ecu' => 'required|string|max:255',
            'module' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only('ecu', 'module', 'brand');
        $data['user_uuid'] = auth()->user()->uuid;
        ECURequest::query()->create($data);


        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('ecu_requests');
    }

    public function destroy($uuid, Request $request)
    {
        ECURequest::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $ecu_requests = ECURequest::query()->orderByDesc('id');
        return Datatables::of($ecu_requests)
            ->filter(function ($query) use ($request) {
                if ($request->module) {
                    $query->where("module", 'Like', "%" . $request->module . "%");
                }
                if ($request->brand) {
                    $query->where("brand", 'Like', "%" . $request->brand . "%");
                }
                if ($request->ecu) {
                    $query->where("ecu", 'Like', "%" . $request->ecu . "%");
                }
            })->addColumn('action', function ($ecu_request) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $ecu_request->uuid . '" ';
                $data_attr .= 'data-module="' . $ecu_request->module . '" ';
                $data_attr .= 'data-brand="' . $ecu_request->brand . '" ';
                $data_attr .= 'data-ecu="' . $ecu_request->ecu . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $ecu_request->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }

}
