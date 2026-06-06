<?php

namespace App\Console\Commands;

use App\Models\Script;
use App\Services\MagicsScriptParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackfillScriptFileSize extends Command
{
    protected $signature = 'scripts:backfill-file-size
                            {--dry-run : Show what would be updated without saving}
                            {--force  : Update even if expected_file_size already set}';

    protected $description = 'Read all script files from S3 and backfill expected_file_size in the scripts table';

    public function handle(MagicsScriptParser $parser): int
    {
        $dryRun = $this->option('dry-run');
        $force  = $this->option('force');

        $query = Script::with('files')->whereHas('files');

        if (!$force) {
            $query->whereNull('expected_file_size');
        }

        $scripts = $query->get();

        if ($scripts->isEmpty()) {
            $this->info('No scripts to process.');
            return 0;
        }

        $this->info("Found {$scripts->count()} script(s) to process...");
        $this->newLine();

        $updated = 0;
        $skipped = 0;

        foreach ($scripts as $script) {
            $scriptFile = $script->files->first();

            if (!$scriptFile) {
                $this->warn("  [{$script->uuid}] No file attached — skipped.");
                $skipped++;
                continue;
            }

            $rawPath = $scriptFile->getRawOriginal('file');

            // جلب الملف من S3
            try {
                $content = Storage::disk('s3')->get($rawPath);
            } catch (\Exception $e) {
                $this->warn("  [{$script->uuid}] ⚠️  Not found on S3: {$rawPath} — skipped.");
                $skipped++;
                continue;
            }

            // تحليل المحتوى — بغض النظر عن الامتداد
            try {
                $parsed       = $parser->parse($content);
                $expectedSize = $parsed['expected_size'];

                if ($dryRun) {
                    $this->line("  [{$script->uuid}] DRY-RUN → expected_file_size = " . number_format($expectedSize) . " bytes");
                } else {
                    $script->update(['expected_file_size' => $expectedSize]);
                    $this->info("  [{$script->uuid}] ✅ " . number_format($expectedSize) . " bytes ({$parsed['patch_count']} patches)");
                }

                $updated++;

            } catch (\Exception $e) {
                $this->warn("  [{$script->uuid}] ⚠️  Parse failed: {$e->getMessage()} — skipped.");
                $skipped++;
            }
        }

        $this->newLine();
        $this->info("Done! {$updated} updated, {$skipped} skipped.");

        return 0;
    }
}
