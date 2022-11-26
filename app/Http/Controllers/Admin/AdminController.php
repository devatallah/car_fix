<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
//use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{

    public function index(Request $request)
    {
//        $permissions = Permission::all();
        return view('portals.admin.admins.index'/*, compact('permissions')*/);

    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'mobile' => 'required|string|max:255|unique:admins',
            'password' => 'required|min:6',
//            'permissions' => 'required|array',
        ];

        $this->validate($request, $rules);

        $data = $request->only('name', 'email', 'mobile');
        $data['password'] = bcrypt($request->password);

        $admin = Admin::query()->create($data);
//        $admin->givePermissionTo($request->permissions);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));

        return redirect('mealy_panel/admins');
    }


    public function update(Admin $admin, Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'string|email|max:255|unique:admins,email,' . $admin->uuid.',uuid',
            'mobile' => 'string|max:255|unique:admins,mobile,' . $admin->uuid.',uuid',
            'password' => 'nullable|min:6',
//            'permissions' => 'nullable|array',
        ];

        $this->validate($request, $rules);
        $data = $request->only('name', 'email', 'mobile');
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $admin->update($data);
//        if ($request->permissions) {
//            $old_permissions = $admin->getAllPermissions()->pluck('name')->toArray();
//            $admin->revokePermissionTo($old_permissions);
//            $admin->givePermissionTo($request->permissions);
//        }else{
//            $old_permissions = $admin->getAllPermissions()->pluck('name')->toArray();
//            $admin->revokePermissionTo($old_permissions);
//        }

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function updateStatus(Request $request)
    {
        $rules = [
            'ids' => 'required',
            'status' => 'required|in:0,1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false]);
        }
        try {
            Admin::query()->whereIn('uuid', explode(',', $request->ids))->update(['status' => $request->status]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
        return response()->json(['status' => true]);
    }
    public function destroy($id, Request $request)
    {
        $admins = Admin::query()->whereIn('uuid', explode(',', $id))->get();
        foreach ($admins as $admin) {
            $admin->email = 'deleted_'.Carbon::now()->timestamp.$admin->email;
            $admin->mobile = 'deleted_'.Carbon::now()->timestamp.$admin->mobile;
            $admin->name = 'deleted_'.Carbon::now()->timestamp.$admin->name;
            $admin->save();
            $admin->delete();
        }
        return response()->json(['status' => true]);
    }

    public function admins(Request $request)
    {
        return view('layout.app');

    }


    public function indexTable(Request $request)
    {
//        dd($request->get('category_id'));
        $admins = Admin::query()->orderByDesc('id');
        return Datatables::of($admins)
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
            })->addColumn('action', function ($admin) {
                $data = ' ';
                $data .= 'data-uuid="' . $admin->uuid . '"';
                $data .= 'data-name="' . $admin->name . '"';
                $data .= 'data-email="' . $admin->email . '"';
                $data .= 'data-mobile="' . $admin->mobile . '"';
//                $perm_ids = implode(',',$admin->getDirectPermissions()->pluck('id')->toArray()).",";
//                $data .= 'data-permissions="' . $perm_ids . '"';
                $data .= ' ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                            '.$data.' data-bs-target="#edit_modal">'.__('edit').'</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                            data-id="' . $admin->uuid .'">'.__('delete').'</button>';
                return $string;
            })
//            ->editColumn('id', 'ID: {{$id}}')

            ->make(true);
    }

}
