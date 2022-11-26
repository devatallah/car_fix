<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function index(Request $request)
    {
        return view('portals.admin.users.index');

    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'string|email|max:255|unique:users',
            'mobile' => 'string|digits_between:8,14|unique:users',
            'password' => 'nullable|string|min:6',
//            'image' => 'nullable|image',
        ];

        $this->validate($request, $rules);

        $data = $request->only('name', 'email', 'mobile');
        $data['password'] = bcrypt($request->password);

//        if ($request->hasFile('image')) {
//            $image = $request->file('image')->store('public');
//            $data['image'] = $image;
//        }
        User::query()->create($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('user/users');
    }

    public function show($id, Request $request)
    {
        $user = User::query()->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function update($id, Request $request)
    {
        $user = User::query()->find($id);
        $rules = [
            'name' => 'required|string|max:255',
            'mobile' => 'string|digits_between:8,14|unique:users,mobile,' . $id . ',uuid',
            'password' => 'nullable|string|min:6',
//            'image' => 'nullable|image',
        ];

        $this->validate($request, $rules);

        $data = $request->only('name', 'email', 'mobile');

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

//        if ($request->hasFile('image')) {
//            $image = $request->file('image')->store('public');
//            $data['image'] = $image;
//        }

        $user->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function destroy($id, Request $request)
    {
        $users = User::query()->whereIn('uuid', explode(',', $id))->get();
        foreach ($users as $user) {
            $time = Carbon::now()->timestamp;
            $user->email = $time . 'deleted_' . $user->email;
            $user->mobile = $time . '_deleted_' . $user->mobile;
            $user->name = $time . '_deleted_' . $user->name;
            $user->save();
            $user->delete();
        }
        return response()->json(['status' => true]);
    }

    public function users(Request $request)
    {
        return view('layout.app');

    }


    public function indexTable(Request $request)
    {
        $users = User::query()->orderByDesc('id');
        return Datatables::of($users)
            ->filter(function ($query) use ($request) {
                if ($request->get('name')) {
                    $query->where('name', 'like', "%{$request->get('name')}%");
                }
                if ($request->get('email')) {
                    $query->where('email', 'like', "%{$request->get('email')}%");
                }
                if ($request->get('mobile')) {
                    $query->where('mobile', 'like', "%{$request->get('mobile')}%");
                }

//                $request->merge(['length' => -1]);
            })->addColumn('action', function ($user) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $user->uuid . '" ';
                $data_attr .= 'data-name="' . $user->name . '" ';
                $data_attr .= 'data-email="' . $user->email . '" ';
                $data_attr .= 'data-mobile="' . $user->mobile . '" ';
                $data_attr .= 'data-password="' . $user->password . '" ';
//                $data_attr .= 'data-image="' . $user->image . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $user->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })
//            ->editColumn('id', 'ID: {{$id}}')

            ->make(true);
    }

}
