<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class SolutionController extends Controller
{

    public function index(Request $request)
    {
        return view('portals.admin.solutions.index');

    }

    public function update(Solution $solution, Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric',
            'is_free' => 'required|boolean',
        ];
        $this->validate($request, $rules);
        $data = $request->only('name', 'price', 'is_free');
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
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric',
            'is_free' => 'required|boolean',
        ];
        $this->validate($request, $rules);
        $data = $request->only('name', 'price', 'is_free');
        Solution::query()->create($data);


        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('solutions');
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
                if ($request->name) {
                    $query->where("name", 'Like', "%" . $request->name . "%");
                }
            })->addColumn('action', function ($solution) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $solution->uuid . '" ';
                $data_attr .= 'data-name="' . $solution->name . '" ';
                $data_attr .= 'data-is_free="' . $solution->is_free . '" ';
                $data_attr .= 'data-price="' . $solution->price . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $solution->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }

}
