<?php

namespace App\Services;

class MagicsScriptApplier
{
    protected MagicsScriptParser $parser;

    public function __construct(MagicsScriptParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Apply a parsed magicsscript to a binary file content.
     *
     * @param string $binaryContent Raw binary content of the ECU file
     * @param array $parsedScript Output from MagicsScriptParser::parse()
     * @return array [
     *   'content' => string (patched binary),
     *   'applied' => int (number of patches applied),
     *   'skipped' => int (number of patches skipped),
     *   'warnings' => string[] (mismatch warnings),
     * ]
     * @throws \InvalidArgumentException if file size doesn't match
     */
    public function apply(string $binaryContent, array $parsedScript): array
    {
        $fileSize = strlen($binaryContent);
        $expectedSize = $parsedScript['expected_size'];

        if ($fileSize !== $expectedSize) {
            throw new \InvalidArgumentException(
                "File size mismatch: expected {$expectedSize} bytes but got {$fileSize} bytes."
            );
        }

        $applied = 0;
        $skipped = 0;
        $warnings = [];

        // Work with a mutable copy
        $result = $binaryContent;

        foreach ($parsedScript['patches'] as $patch) {
            $offset = $patch['offset'];
            $newByte = $patch['new'];
            $originalByte = $patch['original'];

            // Validate offset is within file bounds
            if ($offset < 0 || $offset >= $fileSize) {
                $warnings[] = sprintf(
                    'Line %d: Offset 0x%X is out of bounds (file size: %d bytes). Skipped.',
                    $patch['line'], $offset, $fileSize
                );
                $skipped++;
                continue;
            }

            // Check original byte if provided and not zero
            if ($originalByte !== null && $originalByte !== 0) {
                $currentByte = ord($result[$offset]);
                if ($currentByte !== $originalByte) {
                    $warnings[] = sprintf(
                        'Line %d: Offset 0x%X - expected original byte 0x%02X but found 0x%02X. Patch applied anyway.',
                        $patch['line'], $offset, $originalByte, $currentByte
                    );
                }
            }

            // Apply the patch
            $result[$offset] = chr($newByte);
            $applied++;
        }

        return [
            'content' => $result,
            'applied' => $applied,
            'skipped' => $skipped,
            'warnings' => $warnings,
        ];
    }

    /**
     * Parse and apply a magicsscript from raw script text to binary content.
     *
     * @param string $binaryContent Raw binary ECU file
     * @param string $scriptContent Raw text of .magicsscript file
     * @return array Same as apply()
     */
    public function parseAndApply(string $binaryContent, string $scriptContent): array
    {
        $parsed = $this->parser->parse($scriptContent);
        return $this->apply($binaryContent, $parsed);
    }

    /**
     * Apply multiple scripts sequentially to the same binary.
     *
     * @param string $binaryContent Raw binary ECU file
     * @param array $scriptContents Array of raw .magicsscript text strings
     * @return array [
     *   'content' => string,
     *   'total_applied' => int,
     *   'total_skipped' => int,
     *   'results' => array[] (per-script results),
     * ]
     */
    public function applyMultiple(string $binaryContent, array $scriptContents): array
    {
        $currentContent = $binaryContent;
        $totalApplied = 0;
        $totalSkipped = 0;
        $results = [];

        foreach ($scriptContents as $index => $scriptContent) {
            $result = $this->parseAndApply($currentContent, $scriptContent);
            $currentContent = $result['content'];
            $totalApplied += $result['applied'];
            $totalSkipped += $result['skipped'];
            $results[] = [
                'index' => $index,
                'applied' => $result['applied'],
                'skipped' => $result['skipped'],
                'warnings' => $result['warnings'],
            ];
        }

        return [
            'content' => $currentContent,
            'total_applied' => $totalApplied,
            'total_skipped' => $totalSkipped,
            'results' => $results,
        ];
    }
}
