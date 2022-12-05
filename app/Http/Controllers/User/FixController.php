<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ECU;
use App\Models\User;
use App\Models\Solution;
use App\Models\File;
use App\Models\Fix;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class FixController extends Controller
{

    public function index(Request $request)
    {
        $solutions = Solution::query()->whereHas('brands.ecus')->get();
        $solution = \App\Models\Solution::query()->whereHas('brands.ecus')->with('brands.ecus')->first();
        $brands = [];
        foreach ($solution->brands as $brand){
            foreach ($brand->ecus as $ecu){
                $ecu_list = ['id' => $ecu->uuid, 'text' => $ecu->name];
            }
            $brands[] = ['id' => $brand->uuid, 'text' => $brand->name, 'children' => [$ecu_list]];
        }
        return view('portals.user.fixes.create', compact('solutions', 'brands'));
//        return view('portals.user.fixes.index', compact('solutions', 'brands'));

    }

    public function update(Fix $fix, Request $request)
    {
        $rules = [
            'broken_file' => 'nullable|file',
            'solution_uuid' => 'required',
            'ecu_uuid' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['broken_file', 'solution_uuid', 'brand_uuid', 'ecu_uuid']);
        $fixed_file = ECU::query()->find($request->ecu_uuid);
        if ($request->hasFile('broken_file')) {
            $broken_file = Storage::disk('s3')->putFile('/broken',$request->file('broken_file'), 'public');
//            $broken_file = $request->broken_file('broken_file')->store('public');
            $data['broken_file'] = $broken_file;
        }
        $data['brand_uuid'] = $fixed_file->brand_uuid;
        $data['fixed_file'] = $fixed_file->file;
        $fix->update($data);

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
            'solution_uuid' => 'required',
            'ecu_uuid' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['broken_file', 'solution_uuid', 'ecu_uuid']);
        $ecu = ECU::query()->find($request->ecu_uuid);
        if ($request->hasFile('broken_file')) {
            $broken_file = Storage::disk('s3')->putFile('/broken',$request->file('broken_file'), 'public');
//            $broken_file = $request->file('broken_file')->store('public');
            $data['broken_file'] = $broken_file;
        }
        $data['brand_uuid'] = $ecu->brand_uuid;
        $data['fixed_file'] = $ecu->file;
        $data['ownerable_uuid'] = auth()->user()->uuid;
        $data['ownerable_type'] = User::class;
        $fix = Fix::query()->create($data);
        $solution = Solution::query()->find($request->solution_uuid);
        $brand = Brand::query()->find($ecu->brand_uuid);
        if (!$solution->is_free){
            $user = User::query()->find(auth()->user()->uuid);
            $user->update(['balance' => $user->balance - $solution->price]);
        }
        $file_name = $request->file('broken_file')->getClientOriginalName();
        $file_size = round($request->file('broken_file')->getSize() /1000/1000,2);
        if ($request->ajax()) {
            return response()->json(['status' => true, 'url' => $fix->broken_file, 'brand_name' => $brand->name, 'solution_name' => $solution->name, 'ecu_name' => $ecu->name, 'file_name' => $file_name, 'file_size' => $file_size]);
        }
        Session::flash('success_message', __('item_added'));
        return redirect()->back();
    }

    public function destroy($uuid, Request $request)
    {
        Fix::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $fixes = Fix::query()->orderByDesc('id')->where('ownerable_uuid', auth()->id())->where('ownerable_type', User::class);
        return Datatables::of($fixes)
            ->filter(function ($query) use ($request) {
                if ($request->get('manufacturer_uuid')) {
                    $query->where('manufacturer_uuid', $request->manufacturer_uuid);
                }
                if ($request->get('car_model_uuid')) {
                    $query->where('car_model_uuid', $request->car_model_uuid);
                }
                if ($request->get('category_uuid')) {
                    $query->where('category_uuid', $request->category_uuid);
                }
            })->addColumn('action', function ($fix) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $fix->uuid . '" ';
                $data_attr .= 'data-solution_uuid="' . $fix->solution_uuid . '" ';
                $data_attr .= 'data-brand_uuid="' . $fix->brand_uuid . '" ';
                $data_attr .= 'data-ecu_uuid="' . $fix->ecu_uuid . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $fix->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }

}
