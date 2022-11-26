<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\File;
use App\Models\Fix;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class FixController extends Controller
{

    public function index(Request $request)
    {
        $categories = Category::all();
        $manufacturers = Manufacturer::all();
        return view('portals.admin.fixes.index', compact('categories', 'manufacturers'));

    }

    public function update(Fix $fix, Request $request)
    {
        $rules = [
            'broken_file' => 'nullable|file',
            'category_uuid' => 'required',
            'manufacturer_uuid' => 'required',
            'car_model_uuid' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['broken_file', 'category_uuid', 'manufacturer_uuid', 'car_model_uuid']);
        $fixed_file = File::query()->where(['category_uuid' => $request->category_uuid,
            'manufacturer_uuid' => $request->manufacturer_uuid,
            'car_model_uuid' => $request->car_model_uuid])->first();
        $data['file_uuid'] = $fixed_file->uuid;
        if ($request->hasFile('broken_file')) {
            $broken_file = $request->broken_file('broken_file')->store('public');
            $data['broken_file'] = $broken_file;
        }
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
            'category_uuid' => 'required',
            'manufacturer_uuid' => 'required',
            'car_model_uuid' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['broken_file', 'category_uuid', 'manufacturer_uuid', 'car_model_uuid']);
        $file = File::query()->where(['category_uuid' => $request->category_uuid,
            'manufacturer_uuid' => $request->manufacturer_uuid,
            'car_model_uuid' => $request->car_model_uuid])->first();
        $data['file_uuid'] = $file->uuid;
        if ($request->hasFile('broken_file')) {
            $broken_file = $request->file('broken_file')->store('public');
            $data['broken_file'] = $broken_file;
        }
        $data['fixed_file'] = $file->file;
        $data['ownerable_uuid'] = auth()->user()->uuid;
        $data['ownerable_type'] = Admin::class;
        Fix::query()->create($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('fixes');
    }

    public function destroy($uuid, Request $request)
    {
        Fix::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $fixes = Fix::query()->orderByDesc('id');
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
                $data_attr .= 'data-category_uuid="' . $fix->category_uuid . '" ';
                $data_attr .= 'data-manufacturer_uuid="' . $fix->manufacturer_uuid . '" ';
                $data_attr .= 'data-car_model_uuid="' . $fix->car_model_uuid . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $fix->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }

}
