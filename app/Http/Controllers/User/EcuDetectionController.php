<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BalanceLog;
use App\Models\Brand;
use App\Models\ECU;
use App\Models\Script;
use App\Models\SmartPatch;
use App\Models\SmartPatchGroup;
use App\Services\MagicsScriptApplier;
use App\Services\SmartPatchApplier;
use App\Services\SmartPatchExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EcuDetectionController extends Controller
{
    protected MagicsScriptApplier $applier;
    protected SmartPatchApplier   $smartApplier;
    protected SmartPatchExtractor $extractor;

    public function __construct(
        MagicsScriptApplier   $applier,
        SmartPatchApplier     $smartApplier,
        SmartPatchExtractor   $extractor
    ) {
        $this->applier      = $applier;
        $this->smartApplier = $smartApplier;
        $this->extractor    = $extractor;
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

            // استخراج معرّف الـ ECU من الملف مباشرة (أول string قابل للقراءة ≥ 10 أحرف)
            $ecuSoftwareNumber = $this->extractor->extractEcuSoftwareNumber($binaryContent);

            // البحث عن الـ Smart Patches المطابقة
            $smartPatches = $this->findMatchingSmartPatches($ecuSoftwareNumber, $fileSize);

            $modifications = $smartPatches->map(fn($sp) => [
                'uuid'        => 'sp:' . $sp->uuid,
                'module_name' => optional($sp->module)->name ?? 'Unknown Module',
                'module_uuid' => $sp->module_uuid,
                'is_free'     => (bool) optional($sp->module)->is_free,
                'price'       => (float) (optional($sp->module)->price ?? 0),
                'source'      => 'smart_patch',
            ]);

            // بيانات السيارة — مأخوذة من الـ patch المطابق في DB (لا من الملف)
            $firstPatch = $smartPatches->first();
            $carInfo = [
                'car_make'            => $firstPatch ? $firstPatch->brand_name : null,
                'ecu_type'            => $firstPatch ? $firstPatch->ecu_name   : null,
                'ecu_software_number' => $ecuSoftwareNumber,
                'file_size'           => $fileSize,
                'found'               => $modifications->isNotEmpty(),
            ];

            // حفظ الملف مؤقتاً
            $sessionKey = Str::uuid()->toString();
            $tempPath   = 'ecu_temp/' . $sessionKey . '.bin';
            Storage::disk('local')->put($tempPath, $binaryContent);

            Cache::put('ecu_detect_' . $sessionKey, [
                'car_info'            => $carInfo,
                'temp_path'           => $tempPath,
                'file_name'           => $request->file('file')->getClientOriginalName(),
                'file_size'           => $fileSize,
                'ecu_software_number' => $ecuSoftwareNumber,
            ], now()->addHours(2));

            if ($request->ajax()) {
                return response()->json([
                    'status'         => true,
                    'session'        => $sessionKey,
                    'data'           => $carInfo,
                    'modifications'  => $modifications->values(),
                    // إذا ما لقى شي → أخبر الـ frontend يطلب الاختيار اليدوي
                    'needs_manual'   => $modifications->isEmpty(),
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

            // ─── فصل الـ UUIDs حسب النوع ─────────────────────────────────────────
            // sp:uuid  = calibration بعينها (auto-detect)
            // spg:uuid = group كامل — جرب كل calibrations فيه (manual select)
            $scriptUuids      = [];
            $smartPatchUuids  = [];
            $smartGroupUuids  = [];

            foreach ($request->record_uuids as $rawUuid) {
                if (str_starts_with($rawUuid, 'spg:')) {
                    $smartGroupUuids[] = substr($rawUuid, 4);
                } elseif (str_starts_with($rawUuid, 'sp:')) {
                    $smartPatchUuids[] = substr($rawUuid, 3);
                } else {
                    $scriptUuids[] = $rawUuid;
                }
            }

            // ─── تحميل السجلات ────────────────────────────────────────────────────
            $scripts = Script::whereIn('uuid', $scriptUuids)
                ->whereNull('deleted_at')
                ->with(['files', 'module', 'ecu'])
                ->get();

            // exact calibrations (auto-detect)
            $smartPatches = SmartPatch::whereIn('uuid', $smartPatchUuids)
                ->whereNull('deleted_at')
                ->with(['module', 'ecu'])
                ->get();

            // group calibrations (manual select) — جيب كل calibrations الـ group
            $groupPatches = SmartPatch::whereIn('group_uuid', $smartGroupUuids)
                ->whereNull('deleted_at')
                ->with(['module', 'ecu'])
                ->get();

            // ادمج الاثنين — groupPatches تنضاف بنفس المنطق
            $smartPatches = $smartPatches->merge($groupPatches);

            if ($scripts->isEmpty() && $smartPatches->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No valid modifications found.'], 422);
            }

            // ─── حساب التكلفة (module فريد = تكلفة واحدة) ────────────────────────
            $allModuleUuids = collect();
            $allModuleUuids = $allModuleUuids->merge($scripts->pluck('module_uuid'));
            $allModuleUuids = $allModuleUuids->merge($smartPatches->pluck('module_uuid'));
            $uniqueModuleUuids = $allModuleUuids->unique()->values();

            $totalCost = 0;
            $scripts->each(function ($s) use ($uniqueModuleUuids, &$totalCost) {
                if ($uniqueModuleUuids->contains($s->module_uuid)) {
                    $uniqueModuleUuids = $uniqueModuleUuids->reject(fn($u) => $u === $s->module_uuid);
                    $mod = $s->module;
                    $totalCost += optional($mod)->is_free ? 0 : (float) optional($mod)->price;
                }
            });
            $smartPatches->each(function ($sp) use ($uniqueModuleUuids, &$totalCost) {
                if ($uniqueModuleUuids->contains($sp->module_uuid)) {
                    $uniqueModuleUuids = $uniqueModuleUuids->reject(fn($u) => $u === $sp->module_uuid);
                    $mod = $sp->module;
                    $totalCost += optional($mod)->is_free ? 0 : (float) optional($mod)->price;
                }
            });

            // ─── التحقق من الرصيد ────────────────────────────────────────────────
            $user = auth()->user();
            if ($user->balance < $totalCost) {
                return response()->json(['status' => false, 'message' => 'رصيدك غير كافٍ لتطبيق هذه الحلول.'], 422);
            }

            $currentBinary  = $binaryContent;
            $totalApplied   = 0;
            $totalSkipped   = 0;
            $appliedModules = [];
            $ecuName        = null;

            // ─── تطبيق الـ Scripts (MagicsScriptApplier) ─────────────────────────
            $scriptGroups = $scripts->groupBy(fn($s) => optional($s->module)->name ?? 'unknown');

            foreach ($scriptGroups as $moduleName => $groupScripts) {
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
                        $currentBinary = $result['content'];
                        $totalApplied += $result['applied'];
                        $totalSkipped += $result['skipped'];
                        $appliedModules[] = $moduleName;
                        $ecuName          = $ecuName ?? optional($script->ecu)->name;
                        $groupApplied     = true;
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                if (!$groupApplied) {
                    \Log::warning("applyMods: no compatible script found for module [{$moduleName}]");
                }
            }

            // ─── تطبيق الـ Smart Patches (SmartPatchApplier) ─────────────────────
            $spGroups = $smartPatches->groupBy(fn($sp) => optional($sp->module)->name ?? 'unknown');

            foreach ($spGroups as $moduleName => $groupPatches) {
                $groupApplied = false;

                foreach ($groupPatches as $sp) {
                    try {
                        $result        = $this->smartApplier->applyFromJson($currentBinary, $sp->patch_map);
                        $currentBinary = $result['content'];
                        $totalApplied += $result['applied'];
                        $totalSkipped += $result['skipped'];
                        $appliedModules[] = $moduleName;
                        $ecuName          = $ecuName ?? optional($sp->ecu)->name;
                        $groupApplied     = true;

                        if (!empty($result['warnings'])) {
                            foreach ($result['warnings'] as $warn) {
                                \Log::info("SmartPatch [{$sp->uuid}] warning: {$warn}");
                            }
                        }
                        break;
                    } catch (\Exception $e) {
                        \Log::warning("SmartPatch [{$sp->uuid}] failed: " . $e->getMessage());
                        continue;
                    }
                }

                if (!$groupApplied) {
                    \Log::warning("applyMods: no compatible smart patch found for module [{$moduleName}]");
                }
            }

            if (empty($appliedModules)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'لم يتم العثور على حل متوافق مع ملفك. تأكد من صحة الملف أو تواصل مع الدعم.',
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
        $brands = Brand::whereHas('ecus', fn($q) => $q->whereHas('smartPatchGroups'))
            ->orderBy('name')
            ->get(['uuid', 'name']);

        return response()->json(['status' => true, 'data' => $brands]);
    }

    /**
     * GET /user/detect/ecus?brand_uuid=X
     */
    public function getEcus(Request $request)
    {
        $this->validate($request, ['brand_uuid' => 'required|exists:brands,uuid']);

        $ecus = ECU::where('brand_uuid', $request->brand_uuid)
            ->whereHas('smartPatchGroups')
            ->orderBy('name')
            ->get(['uuid', 'name']);

        return response()->json(['status' => true, 'data' => $ecus]);
    }

    /**
     * GET /user/detect/manual-solutions?ecu_uuid=X
     *
     * يعيد الـ groups (نوع الفيكس) المتاحة لهاذا الـ ECU.
     * كل group = زر واحد للمستخدم بغض النظر عن عدد الـ calibrations بداخله.
     */
    public function getManualSolutions(Request $request)
    {
        $this->validate($request, ['ecu_uuid' => 'required|exists:ecus,uuid']);

        $groups = SmartPatchGroup::where('ecu_uuid', $request->ecu_uuid)
            ->whereNull('deleted_at')
            ->with(['module'])
            ->get();

        $solutions = $groups->map(fn($g) => [
            'uuid'        => 'spg:' . $g->uuid,   // prefix = smart patch group
            'module_name' => optional($g->module)->name ?? 'Unknown Module',
            'module_uuid' => $g->module_uuid,
            'is_free'     => (bool) optional($g->module)->is_free,
            'price'       => (float) (optional($g->module)->price ?? 0),
            'source'      => 'smart_patch',
        ]);

        return response()->json(['status' => true, 'data' => $solutions->values()]);
    }

    /**
     * Find smart patches matching the uploaded file.
     *
     * Priority 1: ecu_software_number — exact calibration match (100% accurate).
     * Priority 2: file_size only       — fallback for patches uploaded before this feature.
     */
    protected function findMatchingSmartPatches(?string $ecuSoftwareNumber, int $fileSize)
    {
        // Priority 1 — exact ECU software number (admin-selected ECU in DB)
        if ($ecuSoftwareNumber) {
            $result = SmartPatch::where('ecu_software_number', $ecuSoftwareNumber)
                ->whereNull('deleted_at')
                ->with(['module', 'ecu.brand'])
                ->get();

            if ($result->isNotEmpty()) {
                return $result;
            }
        }

        // Priority 2 — fallback for older patches that have no software number stored
        return SmartPatch::whereNull('ecu_software_number')
            ->where('file_size', $fileSize)
            ->whereNull('deleted_at')
            ->with(['module', 'ecu.brand'])
            ->get();
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
