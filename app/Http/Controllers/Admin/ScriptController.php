<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ECU;
use App\Models\Module;
use App\Models\Script;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class ScriptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ecus = ECU::get();
        $modules = Module::get();

        return view('portals.admin.scripts.index', compact("ecus", "modules"));
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
            'ecu_uuid' => 'required|exists:ecus,uuid',
            'module_uuid' => 'required|exists:modules,uuid',
        ];
        $this->validate($request, $rules);

        $data = $request->only(['ecu_uuid', 'module_uuid']);

        Script::query()->create($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('scripts');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Script  $script
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Script $script)
    {
        $rules = [
            'ecu_uuid' => 'required|exists:ecus,uuid',
            'module_uuid' => 'required|exists:modules,uuid',
        ];
        $this->validate($request, $rules);

        $data = $request->only(['ecu_uuid', 'module_uuid']);

        $script->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect('scripts');
    }

    public function destroy($uuid)
    {
        $script = Script::query()->whereIn('uuid', explode(',', $uuid))->first();
        $script->files()->delete();
        $script->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $scripts = Script::query()->orderByDesc('id');
        return DataTables::of($scripts)
            ->filter(function ($query) use ($request) {
                if ($request->get('ecu_uuid')) {
                    $query->where('ecu_uuid', $request->ecu_uuid);
                }
                if ($request->get('module_uuid')) {
                    $query->where('module_uuid', $request->module_uuid);
                }
            })->addColumn('action', function ($script) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $script->uuid . '" ';
                $data_attr .= 'data-ecu_uuid="' . $script->ecu_uuid . '" ';
                $data_attr .= 'data-module_uuid="' . $script->module_uuid . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <a href="' . url("admin/script_files?script_uuid=$script->uuid") . '" class="btn btn-sm btn-outline-primary">Files</a>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $script->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }
}