<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ECURequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ECURequestController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'brand' => 'required',
            'ecu' => 'required',
            'module' => 'required',
            'filedata' => 'required'
            // 'file' => [
            //     'required', function ($input, $value) {
            //         return $value->getClientOriginalExtension() == 'bin';
            //     },
            // ]
        ];
        logger('asd'.$request);
        $this->validate($request, $rules);
        $data = $request->only('ecu', 'module', 'brand');

        $user = $request->user('api');
        $data['user_uuid'] = $user->uuid;
        $filename='test';
       // $file=file_put_contents('request.txt',base64_decode($request->filedata));
        //if ($file) {
            $name = time() . '_' . str_replace(' ', '', $filename);
            $filePath = 'ecus/requests/' . $name . '.bin';
            Storage::disk('s3')->put($filePath, base64_decode($request->filedata), ['visibility' => 'public']);
            $data['file'] = $filePath;
        //}

        if ($data['file'] != '') {
            ECURequest::query()->create($data);

            return response()->json([
                'success' => true,
                "message" => "Send Request Done Successfully"
            ]);
        }

        return response()->json([
            'success' => false,
            "message" => "Send Request Faild"
        ]);
    }
}