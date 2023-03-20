<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class ModuleController extends Controller
{

    public function index(Request $request)
    {
        return view('portals.admin.modules.index');
    }

    public function update(Module $module, Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required_if:is_free,0',
            'is_free' => 'required|boolean',
            'note' => 'nullable',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['name', 'price', 'is_free']);
        if ($request->get('note')) {
            $data['note'] = $request->note;
        }
        $module->update($data);

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
            'price' => 'required_if:is_free,0',
            'is_free' => 'required|boolean',
            'note' => 'nullable',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['name', 'price', 'is_free']);
        if ($request->get('note')) {
            $data['note'] = $request->note;
        }
        Module::query()->create($data);


        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('modules');
    }

    public function destroy($uuid, Request $request)
    {
        Module::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $modules = Module::query()->orderByDesc('id');
        return Datatables::of($modules)
            ->filter(function ($query) use ($request) {
                if ($request->name) {
                    $query->where("name", 'Like', "%" . $request->name . "%");
                }
            })->addColumn('action', function ($module) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $module->uuid . '" ';
                $data_attr .= 'data-name="' . $module->name . '" ';
                $data_attr .= 'data-is_free="' . $module->is_free . '" ';
                $data_attr .= 'data-price="' . $module->price . '" ';
                $data_attr .= 'data-note="' . $module->note . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $module->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }
}