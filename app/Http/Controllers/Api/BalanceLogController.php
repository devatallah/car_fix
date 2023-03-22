<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceLogResource;
use App\Models\BalanceLog;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;

class BalanceLogController extends Controller
{

    public function balanceLog(Request $request)
    {
        $user = $request->user('api');

        $balanceLog = $user->balance_log;

        return response()->json([
            'success' => true,
            "message" => "Loaded Successfully",
            'data' => count($balanceLog) ? BalanceLogResource::collection($balanceLog) : null
        ]);
    }

    public function updateBalance(Request $request)
    {
        //return auth()->user();
        $rules = [
            'brand' => 'required',
            'ecu' => 'required',
            'fix_type' => 'required',
        ];
        $this->validate($request, $rules);
        logger($request->fix_type . "fix type");
        $word = " ";
        $fix_type_a='';
        if(strpos($request->fix_type,$word) !== false){
            $fix_type_a = explode(" ", $request->fix_type);
            $fix_type_price = Module::query()->whereIn("uuid", $fix_type_a)->get()->sum("price");
        }else{
        $fix_type=$request->fix_type;
        $fix_type_price = Module::query()->whereIn("uuid", $fix_type)->get("price");
        }
        logger($fix_type_price . "fix_type_price");
        $user = $request->user('api');
        $user = User::find($user->uuid);
        logger($user . "user");
        $old_balance = $user->balance;

        if ($fix_type_price > $old_balance) {
            return response()->json([
                'success' => false,
                "message" => "User Balance Not Enough"
            ]);
        }

        $user->balance = $old_balance - $fix_type_price;
        logger( $user->balance." user->balance");
        $user->save();

        if ($user->wasChanged('balance')) {
            $balanceLog = new BalanceLog();
            $balanceLog->user_uuid = $user->uuid;
            $balanceLog->old_value = $old_balance;
            $balanceLog->new_value = $user->balance;
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