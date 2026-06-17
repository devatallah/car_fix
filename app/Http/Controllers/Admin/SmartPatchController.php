<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ECU;
use App\Models\Module;
use App\Models\SmartPatch;
use App\Services\SmartPatchExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class SmartPatchController extends Controller
{
    protected SmartPatchExtractor $extractor;

    public function __construct(SmartPatchExtractor $extractor)
    {
        $this->extractor = $extractor;
    }

    public function index()
    {
        $ecus    = ECU::orderBy('name')->get();
        $modules = Module::where('name', '!=', 'origin')->orderBy('name')->get();

        return view('portals.admin.smart_patches.index', compact('ecus', 'modules'));
    }

    /**
     * Upload 3 binary files, run the extractor, save the patch_map.
     * POST /admin/smart_patches
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'ecu_uuid'    => 'required|exists:ecus,uuid',
            'module_uuid' => 'required|exists:modules,uuid',
            'ori1'        => 'required|file',
            'mod'         => 'required|file',
            'ori2'        => 'required|file',
        ]);

        try {
            $ori1Content = file_get_contents($request->file('ori1')->getRealPath());
            $modContent  = file_get_contents($request->file('mod')->getRealPath());
            $ori2Content = file_get_contents($request->file('ori2')->getRealPath());

            $result = $this->extractor->extract($ori1Content, $modContent, $ori2Content);

            $patch = SmartPatch::create([
                'ecu_uuid'             => $request->ecu_uuid,
                'module_uuid'          => $request->module_uuid,
                'ecu_software_number'  => $result['ecu_software_number'],
                'file_size'            => $result['file_size'],
                'patch_map'            => json_encode($result),
                'patches_count'        => $result['patches_count'],
                'wildcard_count'       => $result['wildcard_count'],
                'context_size'         => $this->extractor->getContextSize(),
                'gap_tolerance'        => $this->extractor->getGapTolerance(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'status'               => true,
                    'uuid'                 => $patch->uuid,
                    'ecu_software_number'  => $result['ecu_software_number'],
                    'patches_count'        => $result['patches_count'],
                    'wildcard_count'       => $result['wildcard_count'],
                    'clusters_count'       => count($result['clusters']),
                    'file_size'            => $result['file_size'],
                ]);
            }

            Session::flash('success_message', 'Smart patch created successfully.');
            return redirect()->back();

        } catch (\InvalidArgumentException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(string $uuid)
    {
        SmartPatch::whereIn('uuid', explode(',', $uuid))->delete();
        return response()->json(['status' => true]);
    }

    public function indexTable(Request $request)
    {
        $records = SmartPatch::with(['ecu.brand', 'module'])->orderByDesc('id');

        return DataTables::of($records)
            ->filter(function ($query) use ($request) {
                if ($request->ecu_uuid) {
                    $query->where('ecu_uuid', $request->ecu_uuid);
                }
                if ($request->module_uuid) {
                    $query->where('module_uuid', $request->module_uuid);
                }
            })
            ->addColumn('ecu_name',   fn($r) => optional($r->ecu)->name)
            ->addColumn('brand_name', fn($r) => optional(optional($r->ecu)->brand)->name)
            ->addColumn('module_name', fn($r) => optional($r->module)->name)
            ->addColumn('action', function ($r) {
                $s  = '<button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $r->uuid . '">';
                $s .= 'Delete</button>';
                return $s;
            })
            ->make(true);
    }

    /** AJAX helper: return ECUs for a given brand */
    public function getEcusByBrand(Request $request)
    {
        $ecus = ECU::where('brand_uuid', $request->brand_uuid)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['uuid', 'name']);

        return response()->json($ecus);
    }
}
