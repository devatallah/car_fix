<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\EcuDetectionService;
use App\Services\MagicsScriptApplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EcuDetectionController extends Controller
{
    protected EcuDetectionService $detectionService;
    protected MagicsScriptApplier $applier;

    public function __construct(EcuDetectionService $detectionService, MagicsScriptApplier $applier)
    {
        $this->detectionService = $detectionService;
        $this->applier = $applier;
    }

    /**
     * Show the upload page.
     * GET /user/detect
     */
    public function index()
    {
        return view('portals.user.detect.upload');
    }

    /**
     * Detect ECU from uploaded file.
     * POST /user/detect
     */
    public function detect(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|max:131072', // max 128MB
        ]);

        try {
            $binaryContent = file_get_contents($request->file('file')->getRealPath());
            $fileSize = strlen($binaryContent);
            
            // Try detection by signature
            $detection = $this->detectionService->detect($binaryContent);

            // Fallback: try matching by script expected_size if no signatures in DB
            if (!$detection) {
                $detection = $this->detectionService->detectFromScripts($binaryContent);
            }

            // Development fallback: if still no detection, try to find matching ECU by file size
            if (!$detection) {
                // Try to find any ECU file with similar size (within 10%)
                $tolerance = $fileSize * 0.1;
                $matchingEcu = \App\Models\ECUFile::where('file_size', '>=', $fileSize - $tolerance)
                    ->where('file_size', '<=', $fileSize + $tolerance)
                    ->with('ecu', 'ecu.brand')
                    ->first();
                
                if ($matchingEcu) {
                    $detection = [
                        'ecu_uuid'      => $matchingEcu->ecu_uuid,
                        'ecu_file_uuid' => $matchingEcu->uuid,
                        'car_make'      => $matchingEcu->ecu->brand->brand_name ?? 'Unknown Brand',
                        'car_model'     => $matchingEcu->ecu->ecu_name ?? 'Unknown Model',
                        'year_range'    => null,
                        'ecu_type'      => $matchingEcu->ecu->ecu_type ?? 'ECU',
                        'hw_sw_number'  => null,
                        'confidence'    => 'size_proximity',
                        'file_size'     => $fileSize,
                    ];
                } else {
                    // Last resort: generic detection by file size
                    $detection = [
                        'ecu_uuid'      => null,
                        'ecu_file_uuid' => null,
                        'car_make'      => 'Unknown Brand',
                        'car_model'     => 'Unknown Model',
                        'year_range'    => null,
                        'ecu_type'      => 'File Size: ' . number_format($fileSize / 1024 / 1024, 2) . ' MB',
                        'hw_sw_number'  => null,
                        'confidence'    => 'low',
                        'file_size'     => $fileSize,
                    ];
                }
            }

            // Store file temporarily for later use
            $sessionKey = Str::uuid()->toString();
            $tempPath = 'ecu_temp/' . $sessionKey . '.bin';
            Storage::disk('local')->put($tempPath, $binaryContent);

            // Remove non-serializable objects before storing in session
            $detectionData = $detection;
            unset($detectionData['signature']);

            // Store detection result in session
            session()->put('ecu_detect_' . $sessionKey, [
                'detection' => $detectionData,
                'temp_path' => $tempPath,
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_size' => strlen($binaryContent),
            ]);

            // Load available script-based modifications for this ECU
            $modifications = \App\Models\Script::where('ecu_uuid', $detection['ecu_uuid'])
                ->whereNull('deleted_at')
                ->with('module')
                ->get();

            if ($request->ajax()) {
                return response()->json([
                    'status'  => true,
                    'session' => $sessionKey,
                    'data'    => [
                        'car_make'      => $detection['car_make'],
                        'car_model'     => $detection['car_model'],
                        'year_range'    => $detection['year_range'] ?? null,
                        'ecu_type'      => $detection['ecu_type'],
                        'hw_sw_number'  => $detection['hw_sw_number'] ?? null,
                        'confidence'    => $detection['confidence'],
                        'ecu_uuid'      => $detection['ecu_uuid'],
                        'brand_uuid'    => $detection['ecu_uuid'] ? (\App\Models\ECU::find($detection['ecu_uuid'])?->brand_uuid ?? null) : null,
                        'file_size'     => strlen($binaryContent),
                    ],
                    'modifications' => $modifications->map(fn($script) => [
                        'uuid'        => $script->uuid,
                        'module_name' => $script->module->name ?? 'Unknown Module',
                        'module_uuid' => $script->module_uuid,
                    ]),
                ]);
            }

            return redirect()->route('user.detect.show', $sessionKey);

        } catch (\Exception $e) {
            \Log::error('ECU Detection Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($request->ajax()) {
                return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show results page with modification options.
     * GET /user/detect/{session}
     */
    public function show(string $sessionKey)
    {
        $sessionData = session()->get('ecu_detect_' . $sessionKey);

        if (!$sessionData) {
            return redirect()->route('user.detect.index')
                ->with('error', 'Session expired or not found. Please upload the file again.');
        }

        $detection = $sessionData['detection'];

        $modifications = \App\Models\Script::where('ecu_uuid', $detection['ecu_uuid'])
            ->whereNull('deleted_at')
            ->with('module')
            ->get();

        return view('portals.user.detect.results', compact('detection', 'modifications', 'sessionKey', 'sessionData'));
    }

    /**
     * Apply selected modifications to the file.
     * POST /user/detect/{session}/apply
     */
    public function applyMods(Request $request, string $sessionKey)
    {
        $this->validate($request, [
            'record_uuids'   => 'required|array|min:1',
            'record_uuids.*' => 'string',
        ]);

        $sessionData = session()->get('ecu_detect_' . $sessionKey);

        if (!$sessionData) {
            return response()->json(['status' => false, 'message' => 'Session expired. Please upload the file again.'], 422);
        }

        try {
            // Load binary from temp storage
            $binaryContent = Storage::disk('local')->get($sessionData['temp_path']);

            if (!$binaryContent) {
                return response()->json(['status' => false, 'message' => 'Temporary file not found. Please upload again.'], 422);
            }

            // Load all requested scripts with their files
            $scripts = \App\Models\Script::whereIn('uuid', $request->record_uuids)
                ->whereNull('deleted_at')
                ->with('files')
                ->get();

            if ($scripts->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No valid modifications found.'], 422);
            }

            $scriptContents = [];
            foreach ($scripts as $script) {
                $scriptFile = $script->files->first();
                if ($scriptFile) {
                    $s3Path = $scriptFile->getRawOriginal('file');
                    $content = Storage::disk('s3')->get($s3Path);
                    if ($content !== null) {
                        $scriptContents[] = $content;
                    }
                }
            }

            if (empty($scriptContents)) {
                return response()->json(['status' => false, 'message' => 'No script content found on storage.'], 422);
            }

            // Apply scripts sequentially
            $result = $this->applier->applyMultiple($binaryContent, $scriptContents);

            // Build filename
            $baseName   = pathinfo($sessionData['file_name'], PATHINFO_FILENAME);
            $outputName = 'patched_' . $baseName . '.bin';

            // Clean up temp file
            Storage::disk('local')->delete($sessionData['temp_path']);
            session()->forget('ecu_detect_' . $sessionKey);

            return response()->stream(function () use ($result) {
                echo $result['content'];
            }, 200, [
                'Content-Type'        => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $outputName . '"',
                'X-Patches-Applied'   => $result['total_applied'],
                'X-Patches-Skipped'   => $result['total_skipped'],
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Processing error: ' . $e->getMessage()], 500);
        }
    }
}
