<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DTC;
use App\Models\DTCFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class DTCFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dtc = DTC::find($request->dtc_uuid);
        if (!$dtc) {
            return redirect('admin/dtcs');
        }
        return view('portals.admin.dtc_files.index', compact("dtc"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'dtc_uuid' => 'required|exists:dtcs,uuid',
            'file.*' =>  [
                'required', function ($input, $value) {
                    return $value->getClientOriginalExtension() == 'bin';
                }
            ],
        ];
        $this->validate($request, $rules);

        $time_stamp = Carbon::now()->timestamp;
        DB::beginTransaction();
        
        foreach ($request->file as $key => $value) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $charactersLength; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }
            $randomString;
            $item_file = $value;
            $file = Storage::disk('s3')->putFileAs(
                '',
                $item_file,
                'dtcs/file/'.$randomString.'-' . $request->dtc_uuid . '_' . $randomString .'-'.$time_stamp. '.bin',
                ['visibility' => 'public']
            );
            ###3
            // $item_file = $value;
            // $file = Storage::disk('s3')->putFileAs(
            //     '',
            //     $item_file,
            //     'dtcs/file/' . $request->dtc_uuid . '_' . $time_stamp . '.bin',
            //     ['visibility' => 'public']
            // );
            $dtcFile = new DTCFile();
            $dtcFile->dtc_uuid = $request->dtc_uuid;
            $dtcFile->file = $file;
            $dtcFile->save();
            if (!$dtcFile) {
                DB::rollBack();
                $err =  "Create File Record Error";
                return response()->json(['status' => false, 'errors' => $err]);
            }
        }
        DB::commit();
        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_added'));
        return redirect('scripts');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ScriptFile  $scriptFile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DTCFile $dtcFile)
    {
        $rules = [
            'dtc_uuid' => 'required|exists:dtcs,uuid',
            'file' =>  [
                function ($input, $value) {
                    return $value->getClientOriginalExtension() == 'bin';
                }
            ],
        ];
        $this->validate($request, $rules);

        $data = $request->only(['file']);

        $time_stamp = Carbon::now()->timestamp;

        if ($request->hasFile('file')) {
            $file = Storage::disk('s3')->putFileAs(
                '',
                $request->file('file'),
                'dtcs/file/' . $request->dtc_uuid . '_' . $time_stamp . '.bin',
                ['visibility' => 'public']
            );

            $data['file'] = $file;
        }

        $dtcFile->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function destroy($uuid)
    {
        DTCFile::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request, $dtc_uuid)
    {
        $script_files = DTCFile::where('dtc_uuid', $dtc_uuid)->orderByDesc('id')->get();

        return DataTables::of($script_files)
            ->addColumn('action', function ($file) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $file->uuid . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $file->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->make(true);
    }
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}