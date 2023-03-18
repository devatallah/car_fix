<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ];
        $this->validate($request, $rules);

        $user = User::where('email', $request->email)->firstOrFail();

        if ($user && Hash::check($request->password, $user->password)) {
            $user->refresh();
            $result = [
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'license_expire_date' => $user->license_expire_date,
                'subscription_expire_date' => $user->subscription_expire_date,
                'balance' => $user->balance,
                'token' => $user->createToken('user_token')->plainTextToken
            ];

            return response()->json([
                'success' => true,
                "message" => "Login Successfully",
                'data' => $result
            ]);
        } else {
            return response()->json([
                'success' => false,
                "message" => "Credentials Error"
            ]);
        }
    }

    public function profile(Request $request)
    {
        $user = $request->user('api');

        $result = [
            'uuid' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'license_expire_date' => $user->license_expire_date,
            'subscription_expire_date' => $user->subscription_expire_date,
            'balance' => $user->balance,
        ];

        return response()->json([
            'success' => true,
            "message" => "Login Successfully",
            'data' => $result
        ]);
    }

    public function checkAuth(Request $request)
    {
        $rules = [
            'token' => 'required'
        ];
        $this->validate($request, $rules);

        $user = $request->user('api');

        $checkToken = $user->tokens()->where('id', $request->token)->first();

        if (!$checkToken) {
            return response()->json([
                'success' => true,
                "message" => "Invalid Token"
            ]);
        }

        return response()->json([
            'success' => true,
            "message" => "Valid Token"
        ]);
    }

    public function logout(Request $request)
    {
        $request->user('api')->tokens()->delete();

        return response()->json([
            'success' => true,
            "message" => "Logout Successfully"
        ]);
    }
}