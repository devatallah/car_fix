<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceLogResource;
use App\Models\BalanceLog;
use App\Models\User;
use Illuminate\Http\Request;

class BalanceLogController extends Controller
{

    public function balanceLog(Request $request)
    {
        logger("aaaa");
        $user = $request->user('api');
        logger("bbbb");

        $balanceLog = $user->balance_log;
        logger("ccc");

        return response()->json([
            'success' => true,
            "message" => "Loaded Successfully",
            'data' => count($balanceLog) ? BalanceLogResource::collection($balanceLog) : null
        ]);
    }

    public function updateBalance(Request $request)
    {
        $rules = [
            'balance' => 'required|numeric|min:1'
        ];
        $this->validate($request, $rules);

        $user = $request->user('api');
        $user = User::find($user->uuid);
        $old_balance = $user->balance;

        $user->balance = $request->balance;

        $user->save();

        if ($user->wasChanged('balance')) {
            $balanceLog = new BalanceLog();
            $balanceLog->user_uuid = $user->uuid;
            $balanceLog->old_value = $old_balance;
            $balanceLog->new_value = $request->balance;
            $balanceLog->save();

            return response()->json([
                'success' => true,
                "message" => "Balance Updated Successfully"
            ]);
        }

        return response()->json([
            'success' => false,
            "message" => "Balance Updated Faild"
        ]);
    }
}