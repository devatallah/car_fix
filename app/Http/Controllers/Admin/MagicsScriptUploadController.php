<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ECU;
use App\Models\ECUFile;
use App\Models\ECUFileRecord;
use App\Models\Module;
use App\Services\MagicsScriptParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class MagicsScriptUploadController extends Controller
{
    protected MagicsScriptParser $parser;

    public function __construct(MagicsScriptParser $parser)
    {
        $this->parser = $parser;
    }

    public function index(Request $request)
    {
        $ecus    = ECU::orderBy('name')->get();
        $modules = Module::where('name', '!=', 'origin')->orderBy('name')->get();
        return view('portals.admin.magicscript.index', compact('ecus', 'modules'));
    }

    /**
     * Upload and save a .magicsscript file as an ECUFileRecord.
     * POST /admin/magicscript
     */
    public function store(Request $request)
    {
        $rules = [
            'ecu_file_uuid' => 'required|string',
            'module_uuid'   => 'required|string',
            'script_file'   => 'required|file',
        ];
        $this->validate($request, $rules);

        // Validate extension
        $ext = $request->file('script_file')->getClientOriginalExtension();
        if (!in_array(strtolower($ext), ['magicsscript', 'txt'])) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid file type. Only .magicsscript files are accepted.',
            ], 422);
        }

        $scriptContent = file_get_contents($request->file('script_file')->getRealPath());

        // Validate the script
        $validation = $this->parser->validate($scriptContent);
        if (!$validation['valid']) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid script: ' . $validation['error'],
            ], 422);
        }

        $ecuFile = ECUFile::where('uuid', $request->ecu_file_uuid)->first();
        if (!$ecuFile) {
            return response()->json(['status' => false, 'message' => 'ECU File not found.'], 422);
        }

        DB::beginTransaction();
        try {
            $record = new ECUFileRecord();
            $record->ecu_file_uuid  = $ecuFile->uuid;
            $record->module_uuid    = $request->module_uuid;
            $record->file           = null; // no binary file on S3 for script-based records
            $record->script_content = $scriptContent;
            $record->patch_method   = 'script';
            $record->save();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status'      => true,
                    'patch_count' => $validation['patch_count'],
                    'file_size'   => $validation['expected_size'],
                    'record_uuid' => $record->uuid,
                ]);
            }
            Session::flash('success_message', __('item_added'));
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $uuid)
    {
        $rules = [
            'module_uuid' => 'required|string',
        ];
        $this->validate($request, $rules);

        $record = ECUFileRecord::where('uuid', $uuid)->where('patch_method', 'script')->firstOrFail();

        if ($request->hasFile('script_file')) {
            $scriptContent = file_get_contents($request->file('script_file')->getRealPath());
            $validation = $this->parser->validate($scriptContent);
            if (!$validation['valid']) {
                return response()->json(['status' => false, 'message' => 'Invalid script: ' . $validation['error']], 422);
            }
            $record->script_content = $scriptContent;
        }

        $record->module_uuid = $request->module_uuid;
        $record->save();

        if ($request->ajax()) {
            return response()->json(['status' => true]);
        }
        Session::flash('success_message', __('item_edited'));
        return redirect()->back();
    }

    public function destroy($uuid)
    {
        ECUFileRecord::whereIn('uuid', explode(',', $uuid))
            ->where('patch_method', 'script')
            ->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $records = ECUFileRecord::where('patch_method', 'script')
            ->with(['module', 'ecu_file.ecu.brand'])
            ->orderByDesc('id');

        return DataTables::of($records)
            ->filter(function ($query) use ($request) {
                if ($request->ecu_uuid) {
                    $query->whereHas('ecu_file', fn($q) => $q->where('ecu_uuid', $request->ecu_uuid));
                }
                if ($request->module_uuid) {
                    $query->where('module_uuid', $request->module_uuid);
                }
            })
            ->addColumn('ecu_name', fn($r) => optional(optional($r->ecu_file)->ecu)->name)
            ->addColumn('brand_name', fn($r) => optional(optional(optional($r->ecu_file)->ecu)->brand)->name)
            ->addColumn('patch_count', function ($r) {
                try {
                    $parsed = $this->parser->parse($r->getRawOriginal('script_content'));
                    return $parsed['patch_count'];
                } catch (\Exception $e) {
                    return 'N/A';
                }
            })
            ->addColumn('action', function ($r) {
                $d  = 'data-uuid="' . $r->uuid . '" ';
                $d .= 'data-module_uuid="' . $r->module_uuid . '" ';
                $s  = '<button class="edit_btn btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#edit_modal" ' . $d . '>' . __('edit') . '</button>';
                $s .= ' <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $r->uuid . '">' . __('delete') . '</button>';
                return $s;
            })
            ->make(true);
    }

    /**
     * AJAX helper: get ECU files for a selected ECU.
     */
    public function getEcuFiles(Request $request)
    {
        $files = ECUFile::where('ecu_uuid', $request->ecu_uuid)->get(['uuid', 'id']);
        return response()->json($files);
    }
}
