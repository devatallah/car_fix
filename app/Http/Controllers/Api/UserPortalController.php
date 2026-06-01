<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\ECU;
use App\Models\SolutionTemplate;
use App\Services\FileProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserPortalController extends Controller
{
    protected $fileProcessingService;

    public function __construct(FileProcessingService $fileProcessingService)
    {
        $this->fileProcessingService = $fileProcessingService;
    }

    /**
     * جلب جميع البراندات المتاحة
     */
    public function getBrands()
    {
        $brands = Brand::with('ecus')
            ->whereHas('ecus', function ($query) {
                $query->whereHas('solutionTemplates');
            })
            ->get(['uuid', 'name']);

        return response()->json([
            'success' => true,
            'data' => $brands
        ]);
    }

    /**
     * جلب ECU حسب البراند
     */
    public function getEcusByBrand(Request $request)
    {
        $request->validate(['brand_uuid' => 'required|exists:brands,uuid']);

        $ecus = ECU::where('brand_uuid', $request->brand_uuid)
            ->whereHas('solutionTemplates')
            ->get(['uuid', 'name']);

        return response()->json([
            'success' => true,
            'data' => $ecus
        ]);
    }

    /**
     * جلب الحلول المتاحة لـ ECU معين
     */
    public function getSolutionsByEcu(Request $request)
    {
        $request->validate(['ecu_uuid' => 'required|exists:ecus,uuid']);

        $solutions = SolutionTemplate::whereHas('script', function ($query) use ($request) {
            $query->where('ecu_uuid', $request->ecu_uuid);
        })
        ->get(['uuid', 'name', 'description']);

        return response()->json([
            'success' => true,
            'data' => $solutions
        ]);
    }

    /**
     * معالجة الملف وتطبيق السكريبت
     */
    public function processFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'solution_template_uuid' => 'required|exists:solution_templates,uuid'
        ]);

        try {
            $uploadedFile = $request->file('file');
            $solutionTemplateUuid = $request->solution_template_uuid;

            // تطبيق السكريبت
            $modifiedContent = $this->fileProcessingService->applyScript(
                $uploadedFile,
                $solutionTemplateUuid
            );

            // حفظ الملف المعدل
            $storagePath = $this->fileProcessingService->saveModifiedFile(
                $modifiedContent,
                $uploadedFile->getClientOriginalName()
            );

            return response()->json([
                'success' => true,
                'message' => 'تم معالجة الملف بنجاح',
                'download_url' => route('api.download-file', ['path' => base64_encode($storagePath)])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في معالجة الملف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحميل الملف المعدل
     */
    public function downloadFile(Request $request)
    {
        $path = base64_decode($request->path);

        if (!Storage::exists($path)) {
            return response()->json(['error' => 'الملف غير موجود'], 404);
        }

        return Storage::download($path);
    }
}