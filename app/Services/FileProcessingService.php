<?php

namespace App\Services;

use App\Models\SolutionTemplate;
use Illuminate\Support\Facades\Storage;

class FileProcessingService
{
    /**
     * تطبيق السكريبت على الملف
     */
    public function applyScript($uploadedFile, $solutionTemplateUuid)
    {
        // جلب القالب الذكي
        $template = SolutionTemplate::where('uuid', $solutionTemplateUuid)->firstOrFail();

        // قراءة الملف المرفوع
        $userFileContent = file_get_contents($uploadedFile->getRealPath());

        // قراءة محتوى القالب
        $templateContent = $this->getTemplateContent($template);

        // تطبيق الاختلافات على الملف
        $modifiedContent = $this->applyDifferences($userFileContent, $templateContent, $template);

        return $modifiedContent;
    }

    /**
     * جلب محتوى القالب من DB أو الملف
     */
    private function getTemplateContent($template)
    {
        if ($template->template_file) {
            // إذا كان الملف محفوظ في الحقل مباشرة
            return $template->template_file;
        }

        // أو جلبه من Storage
        if (Storage::exists("templates/{$template->uuid}.bin")) {
            return Storage::get("templates/{$template->uuid}.bin");
        }

        return null;
    }

    /**
     * تطبيق الاختلافات على الملف
     */
    private function applyDifferences($userContent, $templateContent, $template)
    {
        $result = $userContent;

        // إذا كان هناك patterns
        if ($template->patterns) {
            $patterns = json_decode($template->patterns, true);

            if (is_array($patterns)) {
                foreach ($patterns as $pattern) {
                    if (isset($pattern['position']) && isset($pattern['modified'])) {
                        // تطبيق التعديل على الموضع المحدد
                        if ($pattern['position'] < strlen($result)) {
                            $result[$pattern['position']] = chr($pattern['modified']);
                        }
                    }
                }
            }
        }

        // إذا كان القالب كاملاً، استخدمه كأساس
        if ($templateContent && strlen($templateContent) === strlen($userContent)) {
            // المقارنة والدمج
            $result = $this->mergeWithTemplate($userContent, $templateContent);
        }

        return $result;
    }

    /**
     * دمج الملف مع القالب
     */
    private function mergeWithTemplate($userContent, $templateContent)
    {
        $result = $userContent;
        $len = strlen($result);

        for ($i = 0; $i < $len; $i++) {
            // يمكن تطبيق منطق ذكي هنا
            // مثل: أخذ القيمة من القالب إذا كانت تختلف عن الأصل
            if ($templateContent[$i] !== $userContent[$i]) {
                $result[$i] = $templateContent[$i];
            }
        }

        return $result;
    }

    /**
     * حفظ الملف المعدل مؤقتاً وإرجاع المسار
     */
    public function saveModifiedFile($content, $originalFileName)
    {
        $fileName = 'modified_' . time() . '_' . $originalFileName;
        $path = "processed/{$fileName}";

        Storage::put($path, $content);

        return $path;
    }
}