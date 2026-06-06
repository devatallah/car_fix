<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BalanceLog;
use App\Models\Brand;
use App\Models\ECU;
use App\Models\Script;
use App\Services\EcuBinAnalyzerService;
use App\Services\MagicsScriptApplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EcuDetectionController extends Controller
{
    protected MagicsScriptApplier $applier;
    protected EcuBinAnalyzerService $analyzer;

    public function __construct(MagicsScriptApplier $applier, EcuBinAnalyzerService $analyzer)
    {
        $this->applier  = $applier;
        $this->analyzer = $analyzer;
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
     * Detect ECU from uploaded file via file size matching on scripts table.
     * POST /user/detect
     */
    public function detect(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|max:131072', // max 128MB
        ]);

        try {
            $binaryContent = file_get_contents($request->file('file')->getRealPath());
            $fileSize      = strlen($binaryContent);

            // تحليل الملف لاستخراج بيانات السيارة والـ ECU
            $originalFilename = $request->file('file')->getClientOriginalName();
            $aiAnalysis       = $this->analyzer->analyze($binaryContent, $originalFilename);

            $carMake  = $aiAnalysis['car_make']  ?? null;
            $ecuType  = $aiAnalysis['ecu_type']  ?? null;

            // استخراج الـ keyword الأساسي من اسم الـ ECU (مثل "MED17" من "MED17 / Tricore (2MB)")
            $ecuKeyword = null;
            if ($ecuType) {
                preg_match('/\b(MED?[0-9]+(?:\.[0-9]+)?|EDC[0-9]+(?:\.[0-9]+)?|SID[0-9]*|ME[0-9]+(?:\.[0-9]+)?|Marelli|Delphi|Denso|Valeo|Tricore)\b/i', $ecuType, $km);
                $ecuKeyword = $km[1] ?? null;
            }

            // البحث عن الـ ECU في الـ DB
            // المستوى 1: brand + ECU keyword معاً
            $matchedEcu = null;
            if ($carMake && $ecuKeyword) {
                $matchedEcu = ECU::whereHas('brand', fn($q) => $q->where('name', 'like', "%{$carMake}%"))
                    ->where('name', 'like', "%{$ecuKeyword}%")
                    ->whereNull('deleted_at')
                    ->first();
            }
            // المستوى 2: ECU keyword بدون قيد على الـ brand
            if (!$matchedEcu && $ecuKeyword) {
                $matchedEcu = ECU::where('name', 'like', "%{$ecuKeyword}%")
                    ->whereNull('deleted_at')
                    ->first();
            }

            // بيانات التحقق المستخرجة من الملف
            $partNumber    = $aiAnalysis['part_number']    ?? null;
            $calibrationId = $aiAnalysis['calibration_id'] ?? null;
            $swVersion     = $aiAnalysis['sw_version']     ?? null;
            $hwVersion     = $aiAnalysis['hw_version']     ?? null;

            // جلب الـ Scripts — فلترة ذكية متعددة المستويات
            $scripts = $this->findMatchingScripts($matchedEcu, $fileSize, $partNumber, $calibrationId, $swVersion);

            // بيانات السيارة — تجي من تحليل الملف مباشرة
            $carInfo = [
                'car_make'        => $aiAnalysis['car_make']       ?? null,
                'car_model'       => $aiAnalysis['car_model']      ?? null,
                'file_size'       => $fileSize,
                'found'           => $scripts->isNotEmpty(),
                'ecu_type'        => $aiAnalysis['ecu_type']       ?? null,
                'ecu_db_match'    => $matchedEcu ? $matchedEcu->name : null,
                'vin'             => $aiAnalysis['vin']             ?? null,
                'vin_offset'      => $aiAnalysis['vin_offset']      ?? null,
                'part_number'     => $partNumber,
                'calibration_id'  => $calibrationId,
                'sw_version'      => $swVersion,
                'hw_version'      => $hwVersion,
                'checksum_16bit'  => $aiAnalysis['checksum_16bit']  ?? null,
                'ai_status'       => $aiAnalysis['analysis_status'] ?? 'partial',
            ];

            // حفظ الملف مؤقتاً
            $sessionKey = Str::uuid()->toString();
            $tempPath   = 'ecu_temp/' . $sessionKey . '.bin';
            Storage::disk('local')->put($tempPath, $binaryContent);

            Cache::put('ecu_detect_' . $sessionKey, [
                'car_info'       => $carInfo,
                'temp_path'      => $tempPath,
                'file_name'      => $request->file('file')->getClientOriginalName(),
                'file_size'      => $fileSize,
                'ecu_uuid'       => $matchedEcu?->uuid,
                'part_number'    => $partNumber,
                'calibration_id' => $calibrationId,
                'sw_version'     => $swVersion,
                'hw_version'     => $hwVersion,
            ], now()->addHours(2));

            if ($request->ajax()) {
                return response()->json([
                    'status'  => true,
                    'session' => $sessionKey,
                    'data'    => $carInfo,
                    'modifications' => $scripts->map(fn($script) => [
                        'uuid'        => $script->uuid,
                        'module_name' => optional($script->module)->name ?? 'Unknown Module',
                        'module_uuid' => $script->module_uuid,
                        'is_free'     => (bool) optional($script->module)->is_free,
                        'price'       => (float) (optional($script->module)->price ?? 0),
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
     * Apply selected modifications to the file.
     * POST /user/detect/{session}/apply
     */
    public function applyMods(Request $request, string $sessionKey)
    {
        $this->validate($request, [
            'record_uuids'   => 'required|array|min:1',
            'record_uuids.*' => 'string',
        ]);

        $sessionData = Cache::get('ecu_detect_' . $sessionKey);

        if (!$sessionData) {
            return response()->json(['status' => false, 'message' => 'Session expired. Please upload the file again.'], 422);
        }

        try {
            $binaryContent = Storage::disk('local')->get($sessionData['temp_path']);

            if (!$binaryContent) {
                return response()->json(['status' => false, 'message' => 'Temporary file not found. Please upload again.'], 422);
            }

            // تحميل الـ Scripts المطلوبة مع ملفاتها
            $scripts = Script::whereIn('uuid', $request->record_uuids)
                ->whereNull('deleted_at')
                ->with(['files', 'module', 'ecu'])
                ->get();

            if ($scripts->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No valid modifications found.'], 422);
            }

            // حساب التكلفة بحسب المجموعات الفريدة (module فريد = تكلفة واحدة)
            $totalCost = $scripts
                ->unique(fn($s) => optional($s->module)->uuid)
                ->sum(fn($s) => optional($s->module)->is_free ? 0 : (float) optional($s->module)->price);

            // التحقق من رصيد المستخدم
            $user = auth()->user();
            if ($user->balance < $totalCost) {
                return response()->json(['status' => false, 'message' => 'رصيدك غير كافٍ لتطبيق هذه الحلول.'], 422);
            }

            // تجميع الـ Scripts حسب module_name
            // كل مجموعة تمثل نوع fix واحد (DPF, EGR, ...)
            $groups = $scripts->groupBy(fn($s) => optional($s->module)->name ?? 'unknown');

            // تطبيق كل مجموعة على الملف الناتج من المجموعة السابقة
            $currentBinary  = $binaryContent;
            $totalApplied   = 0;
            $totalSkipped   = 0;
            $appliedModules = []; // أسماء الـ modules التي تطبّقت فعلاً
            $ecuName        = null;

            foreach ($groups as $moduleName => $groupScripts) {
                $groupApplied = false;

                foreach ($groupScripts as $script) {
                    $scriptFile = $script->files->first();
                    if (!$scriptFile) continue;

                    $rawPath   = $scriptFile->getRawOriginal('file');
                    $publicUrl = 'https://mycarfixbucket.s3.eu-west-1.amazonaws.com/' . $rawPath;
                    $content   = @file_get_contents($publicUrl);
                    if ($content === false || $content === null) continue;

                    try {
                        $result        = $this->applier->parseAndApply($currentBinary, $content);
                        $currentBinary = $result['content'];  // الناتج يصير مدخل المجموعة التالية
                        $totalApplied += $result['applied'];
                        $totalSkipped += $result['skipped'];
                        $appliedModules[] = $moduleName;
                        $ecuName          = $ecuName ?? optional($script->ecu)->name;
                        $groupApplied = true;
                        break; // ✅ نجح — انتقل للمجموعة التالية
                    } catch (\Exception $e) {
                        continue; // ⏭ هذا السكريبت غير متوافق، جرّب التالي
                    }
                }

                // لو كل سكريبتات المجموعة فشلت
                if (!$groupApplied) {
                    \Log::warning("applyMods: no compatible script found for module [{$moduleName}]");
                }
            }

            if (empty($appliedModules)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'لم يتم العثور على سكريبت متوافق مع ملفك. تأكد من صحة الملف أو تواصل مع الدعم.',
                ], 422);
            }

            // نتيجة نهائية
            $result = ['content' => $currentBinary, 'total_applied' => $totalApplied, 'total_skipped' => $totalSkipped];

            // خصم الرصيد وتسجيل العملية
            DB::transaction(function () use ($user, $totalCost) {
                $oldBalance = $user->balance;
                $newBalance = $oldBalance - $totalCost;
                $user->update(['balance' => $newBalance]);
                BalanceLog::create([
                    'user_uuid' => $user->uuid,
                    'old_value' => $oldBalance,
                    'new_value' => $newBalance,
                ]);
            });

            // لا نحذف الملف المؤقت ولا الـ cache — المستخدم قد يطبّق fix إضافي على نفس الملف
            // يتنظّفون تلقائياً بعد انتهاء مدة الـ cache (ساعتان)

            $ecuName    = $ecuName ?? 'ECU';
            $fixNames   = implode(', ', array_unique($appliedModules));
            $outputName = 'magic_solution_(' . $ecuName . ' - ' . $fixNames . ').bin';

            return response()->stream(function () use ($result) {
                echo $result['content'];
            }, 200, [
                'Content-Type'        => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $outputName . '"',
                'X-Patches-Applied'   => $result['total_applied'],
                'X-Patches-Skipped'   => $result['total_skipped'],
                'X-New-Balance'       => auth()->user()->fresh()->balance,
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Processing error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * GET /user/detect/brands
     * All brands that have at least one ECU with scripts.
     */
    public function getBrands()
    {
        $brands = Brand::whereHas('ecus', fn($q) => $q->whereHas('scripts'))
            ->orderBy('name')
            ->get(['uuid', 'name']);

        return response()->json(['status' => true, 'data' => $brands]);
    }

    /**
     * GET /user/detect/ecus?brand_uuid=X
     * ECUs for a specific brand that have scripts.
     */
    public function getEcus(Request $request)
    {
        $this->validate($request, ['brand_uuid' => 'required|exists:brands,uuid']);

        $ecus = ECU::where('brand_uuid', $request->brand_uuid)
            ->whereHas('scripts')
            ->orderBy('name')
            ->get(['uuid', 'name']);

        return response()->json(['status' => true, 'data' => $ecus]);
    }

    /**
     * GET /user/detect/manual-solutions?ecu_uuid=X
     * Available solutions (modules) for a specific ECU.
     */
    public function getManualSolutions(Request $request)
    {
        $this->validate($request, ['ecu_uuid' => 'required|exists:ecus,uuid']);

        $scripts = Script::where('ecu_uuid', $request->ecu_uuid)
            ->whereNull('deleted_at')
            ->with(['module'])
            ->get();

        $solutions = $scripts->map(fn($script) => [
            'uuid'        => $script->uuid,
            'module_name' => optional($script->module)->name ?? 'Unknown Module',
            'module_uuid' => $script->module_uuid,
            'is_free'     => (bool) optional($script->module)->is_free,
            'price'       => (float) (optional($script->module)->price ?? 0),
        ]);

        return response()->json(['status' => true, 'data' => $solutions]);
    }

    /**
     * Smart multi-layer script matching.
     *
     * Layer 1 (best): ECU + file_size + at least one verification field matches
     * Layer 2: ECU + file_size (no verification constraint)
     * Layer 3: file_size only (fallback when no ECU match)
     */
    protected function findMatchingScripts(?ECU $ecu, int $fileSize, ?string $partNumber, ?string $calibrationId, ?string $swVersion)
    {
        $with = ['module', 'ecu', 'ecu.brand'];

        if ($ecu) {
            $baseQuery = Script::where('ecu_uuid', $ecu->uuid)
                ->where('expected_file_size', $fileSize)
                ->whereNull('deleted_at');

            // Layer 1: تحقق من حقول التحقق — على الأقل واحدة تنطبق
            $hasVerificationData = $partNumber || $calibrationId || $swVersion;

            if ($hasVerificationData) {
                $precise = (clone $baseQuery)->where(function ($q) use ($partNumber, $calibrationId, $swVersion) {
                    $q->where(function ($inner) use ($partNumber, $calibrationId, $swVersion) {
                        // سكريبتات مقيدة بحقول التحقق وتطابق الملف
                        if ($partNumber)    $inner->orWhere('part_number',    $partNumber);
                        if ($calibrationId) $inner->orWhere('calibration_id', $calibrationId);
                        if ($swVersion)     $inner->orWhere('sw_version',     $swVersion);
                    })
                    // أو سكريبتات غير مقيدة (ما فيها حقول تحقق) تُعرض دائماً
                    ->orWhereNull('part_number');
                })->with($with)->get();

                if ($precise->isNotEmpty()) {
                    return $precise;
                }
            }

            // Layer 2: ECU + file_size بدون قيد على حقول التحقق
            $byEcu = $baseQuery->with($with)->get();
            if ($byEcu->isNotEmpty()) {
                return $byEcu;
            }
        }

        // Layer 3: file_size فقط (آخر fallback)
        return Script::where('expected_file_size', $fileSize)
            ->whereNull('deleted_at')
            ->with($with)
            ->get();
    }
}
