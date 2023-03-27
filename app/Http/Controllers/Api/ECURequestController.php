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

        $file=file_put_contents('request.txt',$request->filedata);
        if ($file) {
            $name = time() . '_' . str_replace(' ', '', $file->getClientOriginalName());
            $filePath = 'ecus/requests/' . $name;
            Storage::disk('s3')->put($filePath, file_get_contents($file), ['visibility' => 'public']);
            $data['file'] = $filePath;
        }

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