<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SettingController extends Controller
{

    public function index()
    {
        $setting = Setting::first();
        return view('portals.admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $rules = [
            'api_key' => 'required'
        ];

        $this->validate($request, $rules);

        $data = $request->only('api_key');

        Setting::updateOrCreate(
            ['id' => 1],
            $data
        );

        Session::flash('success', 'Operation Success');

        return redirect()->back();
    }
}
