<?php

namespace App\Console\Commands;

use App\Models\Script;
use App\Models\SolutionTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AnalyzeScripts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scripts:analyze {path} {ecu_uuid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze scripts in a directory and create solution templates for a specific ECU';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->argument('path');
        $ecuUuid = $this->argument('ecu_uuid');

        // جلب السكريبتات الحالية للـ ECU
        $scripts = Script::where('ecu_uuid', $ecuUuid)->with('files')->get();

        if ($scripts->isEmpty()) {
            $this->error('No scripts found for this ECU');
            return 1;
        }

        // قراءة الملفات من المسار (افتراضيًا نسخها إلى storage أو شيء)
        // هنا افتراض بسيط: افترض أن الملفات في storage/app/scripts/
        // يمكن تعديل لقراءة من path

        $this->info('Analyzing scripts...');

        // منطق تحليل بسيط: تجميع الأنماط الشائعة
        $patterns = [];

        foreach ($scripts as $script) {
            foreach ($script->files as $file) {
                // افترض أن file->file هو path
                $content = Storage::get($file->file); // أو file_get_contents إذا كان path كامل
                // تحليل المحتوى، مثل البحث عن أنماط ثنائية شائعة
                // هذا مثال بسيط
                $hash = md5($content);
                if (!isset($patterns[$hash])) {
                    $patterns[$hash] = ['content' => $content, 'scripts' => []];
                }
                $patterns[$hash]['scripts'][] = $script->uuid;
            }
        }

        // إنشاء قوالب للأنماط الأكثر شيوعًا
        foreach ($patterns as $hash => $data) {
            if (count($data['scripts']) > 1) { // إذا كان النمط مشترك في عدة سكريبتات
                SolutionTemplate::create([
                    'script_uuid' => $data['scripts'][0], // ربط بالسكريبت الأول
                    'name' => 'Template for ' . $hash,
                    'description' => 'Auto-generated template',
                    'patterns' => json_encode(['hash' => $hash]),
                    'template_file' => $data['content'],
                ]);
                $this->info('Created template for pattern ' . $hash);
            }
        }

        $this->info('Analysis complete');
        return 0;
    }
}