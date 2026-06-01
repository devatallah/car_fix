<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ECUFileRecord;
use App\Models\Module;
use App\Services\MagicsScriptApplier;
use App\Services\MagicsScriptParser;
use Illuminate\Http\Request;

class MagicsScriptController extends Controller
{
    protected MagicsScriptParser $parser;
    protected MagicsScriptApplier $applier;

    public function __construct(MagicsScriptParser $parser, MagicsScriptApplier $applier)
    {
        $this->parser = $parser;
        $this->applier = $applier;
    }

    /**
     * Apply a .magicsscript to an uploaded ECU binary file.
     * POST /user/magicscript/apply
     *
     * Accepts:
     *   - file: binary ECU file (.bin)
     *   - record_uuid: uuid of an ECUFileRecord with patch_method='script'
     *     OR
     *   - script_file: uploaded .magicsscript file directly
     */
    public function apply(Request $request)
    {
        $rules = [
            'file' => 'required|file',
        ];

        if (!$request->hasFile('script_file') && !$request->filled('record_uuid')) {
            return response()->json([
                'status' => false,
                'message' => 'Provide either a script_file or a record_uuid.',
            ], 422);
        }

        $this->validate($request, $rules);

        try {
            $binaryContent = file_get_contents($request->file('file')->getRealPath());

            // Determine script content source
            if ($request->hasFile('script_file')) {
                $scriptContent = file_get_contents($request->file('script_file')->getRealPath());
            } else {
                $record = ECUFileRecord::where('uuid', $request->record_uuid)
                    ->where('patch_method', 'script')
                    ->firstOrFail();

                $scriptContent = $record->getRawOriginal('script_content');

                if (empty($scriptContent)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'This record has no script content.',
                    ], 422);
                }
            }

            $result = $this->applier->parseAndApply($binaryContent, $scriptContent);

            // Build output filename
            $originalName = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
            $outputName = $originalName . '_MagicScript_patched.bin';

            // Return patched file as download
            return response($result['content'], 200, [
                'Content-Type'        => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $outputName . '"',
                'X-Patches-Applied'   => $result['applied'],
                'X-Patches-Skipped'   => $result['skipped'],
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate a .magicsscript file without applying it.
     * POST /user/magicscript/validate
     */
    public function validate_script(Request $request)
    {
        $this->validate($request, ['script_file' => 'required|file']);

        $scriptContent = file_get_contents($request->file('script_file')->getRealPath());
        $result = $this->parser->validate($scriptContent);

        return response()->json([
            'status'        => $result['valid'],
            'message'       => $result['valid'] ? 'Valid script.' : $result['error'],
            'patch_count'   => $result['patch_count'],
            'expected_size' => $result['expected_size'],
        ]);
    }
}
