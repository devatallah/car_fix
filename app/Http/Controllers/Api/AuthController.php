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
            'password' => 'required',
            'login_id' => 'required',
        ];
        $this->validate($request, $rules);

        $user = User::where('email', $request->email)->firstOrFail();

        if ($user && Hash::check($request->password, $user->password)){
            logger(DateTime($user->license_expire_date)>= date("Y/m/d") );
            logger(date("Y/m/d") );
            logger($user->license_expire_date );
            if(DateTime($user->license_expire_date) < date("Y/m/d")){
                return response()->json([
                    'success' => false,
                    "message" => "your license expired "
                ]);
            }
            $loginID = $request->login_id;
            $userLoginID = $user->login_id;

            if ($userLoginID == null || $userLoginID == "") {
                $user->update(['login_id' => $loginID]);
            } else if ($userLoginID && $userLoginID != $loginID) {
                return response()->json([
                    'success' => false,
                    "message" => "Credentials Error"
                ]);
            }

            $user->refresh();
            $result = [
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