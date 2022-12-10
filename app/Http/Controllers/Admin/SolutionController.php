<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ECU;
use App\Models\Module;
use App\Models\File;
use App\Models\Solution;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SolutionController extends Controller
{

    public function index(Request $request)
    {
        $modules = Module::all();
        $brands = Brand::all();
        return view('portals.admin.solutions.index', compact('modules', 'brands'));

    }
    public function create(Request $request)
    {
        $modules = Module::query()->whereHas('brands.ecus')->get();
        $module = \App\Models\Module::query()->whereHas('brands.ecus')->with('brands.ecus')->first();
        $brands = [];
        foreach ($module->brands as $brand){
            foreach ($brand->ecus as $ecu){
                $ecu_list[] = ['id' => $ecu->uuid, 'text' => $ecu->name];
            }
            $brands[] = ['id' => $brand->uuid, 'text' => $brand->name, 'children' => $ecu_list];
        }
        return view('portals.admin.solutions.create', compact('modules', 'brands'));
//        return view('portals.user.solutions.index', compact('modules', 'brands'));

    }

    public function update(Solution $solution, Request $request)
    {
        $rules = [
            'broken_file' => 'nullable|file',
            'module_uuid' => 'required',
            'brand_uuid' => 'required',
            'ecu_uuid' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['broken_file', 'module_uuid', 'brand_uuid', 'ecu_uuid']);
        $ecu = ECU::query()->find($request->ecu_uuid);
        if ($request->hasFile('broken_file')) {
            $broken_file = Storage::disk('s3')->putFile('/broken',$request->file('broken_file'), 'public');
//            $broken_file = $request->broken_file('broken_file')->store('public');
            $data['broken_file'] = $broken_file;
        }
        $data['fixed_file'] = $ecu->file;
        $solution->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $rules = [
            'broken_file' => 'required|file',
            'module_uuid' => 'required',
            'ecu_uuid' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['broken_file', 'module_uuid', 'ecu_uuid']);
        $ecu = ECU::query()->find($request->ecu_uuid);
        if ($request->hasFile('broken_file')) {
            $broken_file = Storage::disk('s3')->putFile('/broken',$request->file('broken_file'), 'public');
//            $broken_file = $request->file('broken_file')->store('public');
            $data['broken_file'] = $broken_file;
        }
        $data['brand_uuid'] = $ecu->brand_uuid;
        $data['fixed_file'] = $ecu->getRawOriginal()['file'];
        $data['ownerable_uuid'] = auth()->user()->uuid;
        $data['ownerable_type'] = Admin::class;
        $solution = Solution::query()->create($data);
        $module = Module::query()->find($request->module_uuid);
        $brand = Brand::query()->find($ecu->brand_uuid);
        if (!$module->is_free){
            $user = User::query()->find(auth()->user()->uuid);
            $user->update(['balance' => $user->balance - $module->price]);
        }
        $file_name = $request->file('broken_file')->getClientOriginalName();
        $file_size = round($request->file('broken_file')->getSize() /1000/1000,2);
        if ($request->ajax()) {
            return response()->json(['status' => true, 'url' => $solution->fixed_file, 'brand_name' => $brand->name, 'module_name' => $module->name, 'ecu_name' => $ecu->name, 'file_name' => $file_name, 'file_size' => $file_size]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect()->back();
    }

    public function destroy($uuid, Request $request)
    {
        Solution::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $solutions = Solution::query()->orderByDesc('id');
        return Datatables::of($solutions)
            ->filter(function ($query) use ($request) {
                if ($request->get('brand_uuid')) {
                    $query->where('brand_uuid', $request->brand_uuid);
                }
                if ($request->get('module_uuid')) {
                    $query->where('module_uuid', $request->module_uuid);
                }
            })->addColumn('action', function ($solution) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $solution->uuid . '" ';
                $data_attr .= 'data-module_uuid="' . $solution->module_uuid . '" ';
                $data_attr .= 'data-brand_uuid="' . $solution->brand_uuid . '" ';
                $data_attr .= 'data-ecu_uuid="' . $solution->ecu_uuid . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $solution->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }

}
