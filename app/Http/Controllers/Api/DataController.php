<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ScriptFilesResource;
use App\Models\Brand;
use App\Models\ECU;
use App\Models\Module;
use App\Models\Script;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function brands()
    {
        $brands = Brand::query()->whereHas('ecus')->get();

        return response()->json([
            'success' => true,
            "message" => "Loaded Successfully",
            "data" => BrandResource::collection($brands)
        ]);
    }

    public function scripts(Request $request)
    {

        $rules = [
            'brand' => 'required',
            'ecu' => 'required',
            'fix_type' => 'required',
        ];
        $this->validate($request, $rules);

        $brand = Brand::findOrFail($request->brand);

        $ecu = ECU::where("brand_uuid", $brand->uuid)->where("uuid", $request->ecu)->first();

        $data = [];

        if ($ecu) {
            $fix_type = explode(",", $request->fix_type);

            $modules = Module::query()->whereIn("uuid", $fix_type)->get()->pluck("uuid")->toArray();

            if (count($modules)) {

                $scripts = Script::whereHas("files")->whereIn("module_uuid", $modules)->where('ecu_uuid', $ecu->uuid)->get();
                if (count($scripts)) {
                    foreach ($scripts as $item) {
                        $row = [
                            $brand->name . '-' . $ecu->name . '-' . $item->module->name => ScriptFilesResource::collection($item->files),
                        ];
                        array_push($data, $row);
                    }
                    
                    return response()->json([
                        'success' => true,
                        "message" => "Loaded Successfully",
                        "data" => $data
                    ]);
                }

                return response()->json([
                    'success' => false,
                    "message" => "Scripts Not Found",
                ]);
            }

            return response()->json([
                'success' => false,
                "message" => "Fix Type Not Found",
            ]);
        }

        return response()->json([
            'success' => false,
            "message" => "ECU Not Found",
        ]);
    }
}