<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{


    public function create(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'string|email|max:255|unique:users',
            'mobile' => 'string|unique:users',
            'password' => 'required|string|min:6',
            'license_expire_date' => 'required|date',
            'subscription_expire_date' => 'required|date',
            'balance' => 'required',
        ];
        $this->validate($request, $rules);

        $data = [];

        $data = $request->only('name', 'email', 'mobile', 'license_expire_date', 'subscription_expire_date', 'balance');
        $data['password'] = bcrypt($request->password);

        User::create($data);

        return response()->json([
            'success' => true,
            "message" => "Created Successfully",
        ]);
    }

    public function updateBalance(Request $request)
    {
        $rules = [
            'email' => 'required|exists:users,email',
            'balance' => 'required',
        ];
        $this->validate($request, $rules);

        $user = User::where('email', $request->email);

        $data = [];

        $data['balance'] = $request->balance;

        $user->update($data);

        return response()->json([
            'success' => true,
            "message" => "Updated Successfully",
        ]);
    }
}
