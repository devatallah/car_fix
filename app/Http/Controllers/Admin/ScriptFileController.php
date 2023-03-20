<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Script;
use App\Models\ScriptFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ScriptFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $script = Script::find($request->script_uuid);
        if (!$script) {
            return redirect('admin/scripts');
        }
        return view('portals.admin.script_files.index', compact("script"));
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
            'script_uuid' => 'required|exists:scripts,uuid',
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
            $item_file = $value;
            $file = Storage::disk('s3')->putFileAs(
                '',
                $item_file,
                'scripts/file/' . $request->script_uuid . '_' . $time_stamp . '.bin',
                ['visibility' => 'public']
            );
            $scriptFile = new ScriptFile();
            $scriptFile->script_uuid = $request->script_uuid;
            $scriptFile->file = $file;
            $scriptFile->save();
            if (!$scriptFile) {
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
    public function update(Request $request, ScriptFile $scriptFile)
    {
        $rules = [
            'script_uuid' => 'required|exists:scripts,uuid',
            'file' =>  [
                function ($input, $value) {
                    return $value->getClientOriginalExtension() == 'txt';
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
                'scripts/file/' . $request->script_uuid . '_' . $time_stamp . '.txt',
                ['visibility' => 'public']
            );

            $data['file'] = $file;
        }

        $scriptFile->update($data);

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));

        return redirect()->back();
    }

    public function destroy($uuid)
    {
        ScriptFile::query()->whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request, $script_uuid)
    {
        $script_files = ScriptFile::where('script_uuid', $script_uuid)->orderByDesc('id')->get();

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
}