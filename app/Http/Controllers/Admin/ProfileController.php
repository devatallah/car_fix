<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function update(Request $request){
        $user_id = auth('admin')->id();
        $user = Admin::query()->find($user_id);

        $rules = [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:admins,email,' . $user_id.',uuid',
            'mobile' => 'string|max:255|unique:admins,mobile,' . $user_id.',uuid',
        ];
        $this->validate($request, $rules);
        $data = $request->only('name', 'email');
        $user->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', 'تم التعديل');
        return redirect('mealy_panel/profile');

    }

    public function changePassword(Request $request)
    {
        $user = Admin::query()->find(auth('admin')->id());
        $rules = [
            'current_password' => 'required|hash_check:' . $user->getAttribute('password'),
            'password' => 'required|confirmed|string|min:6',
        ];
        $this->validate($request, $rules);

        $user->update(['password' => bcrypt($request->get('password'))]);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', 'تم التعديل');

        return redirect('mealy_panel/profile#change_password');
    }

}
