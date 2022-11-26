<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        return view('portals.admin.categories.index');

    }

    public function update(Category $category, Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'is_free' => 'required|boolean',
        ];
        $this->validate($request, $rules);
        $data = $request->only('name', 'price', 'is_free');
        $category->update($data);

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
            'price' => 'required|numeric',
            'is_free' => 'required|boolean',
        ];
        $this->validate($request, $rules);
        $data = $request->only('name', 'price', 'is_free');
        Category::query()->create($data);


        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('categories');
    }

    public function destroy($uuid, Request $request)
    {
        $categories = Category::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $categories = Category::query()->orderByDesc('id');
        return Datatables::of($categories)
            ->filter(function ($query) use ($request) {
                if ($request->name) {
                    $query->where("name", 'Like', "%" . $request->name . "%");
                }
            })->addColumn('action', function ($category) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $category->uuid . '" ';
                $data_attr .= 'data-icon="' . $category->icon . '" ';
                $data_attr .= 'data-name="' . $category->name . '" ';
                $data_attr .= 'data-is_free="' . $category->is_free . '" ';
                $data_attr .= 'data-price="' . $category->price . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $category->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }

}
