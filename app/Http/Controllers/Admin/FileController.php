<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\File;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class FileController extends Controller
{

    public function index(Request $request)
    {
        $categories = Category::all();
        $manufacturers = Manufacturer::all();
        return view('portals.admin.files.index', compact('categories', 'manufacturers'));

    }

    public function update(File $file, Request $request)
    {
        $rules = [
            'file' => 'nullable|file',
            'category_uuid' => 'required',
            'manufacturer_uuid' => 'required',
            'car_model_uuid' => 'required',
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['name', 'file', 'category_uuid', 'image', 'manufacturer_uuid', 'car_model_uuid']);
        if ($request->hasFile('file')) {
            $fixed_file = $request->file('file')->store('public');
            $data['file'] = $fixed_file;
        }
        $file->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $rules = [
            'file' => 'required|file',
            'category_uuid' => 'required',
            'manufacturer_uuid' => 'required',
            'car_model_uuid' => 'required',
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only(['name', 'file', 'category_uuid', 'manufacturer_uuid', 'car_model_uuid']);
        if ($request->hasFile('file')) {
            $file = $request->file('file')->store('public');
            $data['file'] = $file;
        }
        $file = File::query()->create($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('files');
    }

    public function destroy($uuid, Request $request)
    {
        $files = File::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $files = File::query()->orderByDesc('id');
        return Datatables::of($files)
            ->filter(function ($query) use ($request) {
                if ($request->name) {
                    $query->where("name", 'Like', "%" . $request->name . "%");
                }
                if ($request->get('manufacturer_uuid')) {
                    $query->where('manufacturer_uuid', $request->manufacturer_uuid);
                }
                if ($request->get('car_model_uuid')) {
                    $query->where('car_model_uuid', $request->car_model_uuid);
                }
                if ($request->get('category_uuid')) {
                    $query->where('category_uuid', $request->category_uuid);
                }
            })->addColumn('action', function ($file) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $file->uuid . '" ';
                $data_attr .= 'data-category_uuid="' . $file->category_uuid . '" ';
                $data_attr .= 'data-manufacturer_uuid="' . $file->manufacturer_uuid . '" ';
                $data_attr .= 'data-car_model_uuid="' . $file->car_model_uuid . '" ';
                $data_attr .= 'data-name="' . $file->name . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $file->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }

}
