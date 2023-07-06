<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\DTCBrandResource;
use App\Http\Resources\DTCResource;
use App\Http\Resources\ScriptFilesResource;
use App\Models\Brand;
use App\Models\DTC;
use App\Models\ECU;
use App\Models\Module;
use App\Models\Script;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function brands()
    {
        $brands = Brand::query()->with('ecus', function($q) {
            $q->whereHas('scripts', function($query) {
                $query->whereHas('files');
            });
        })->get();
        foreach ($brands as $key => $brand) {
            // Check if the brand doesn't have associated ECUs
            if ($brand->ecus->isEmpty()) {
                // Remove the brand from the list
                unset($brands[$key]);
            }
            // Access ECU data for the b
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

    public function dtc_brands()
    {
        $brands_uuid = DTC::get()->pluck('brand_uuid')->toArray();
        $ecus_uuid = DTC::get()->pluck('ecu_uuid')->toArray();

        $brands = Brand::query()->with('ecus', function ($q) use ($ecus_uuid) {
                        $q->whereIn('uuid', $ecus_uuid);
        })->whereIn('uuid', $brands_uuid)->get();


        $data = DTCBrandResource::collection($brands);


        return response()->json([
            'success' => true,
            "message" => "Loaded Successfully",
            "data" => $data
        ]);
    }

    public function dtc(Request $request)
    {
        $rules = [
            'brand' => 'required|exists:dtcs,brand_uuid',
            'ecu' => 'required|exists:dtcs,ecu_uuid',
        ];
        $this->validate($request, $rules);

        $dtc = DTC::where('brand_uuid', $request->brand)->where('ecu_uuid', $request->ecu)->first();

        $data = new DTCResource($dtc);

        return response()->json([
            'success' => true,
            "message" => "Loaded Successfully",
            "data" => $data
        ]);
    }
}