<?php

namespace App\Services;

class MagicsScriptParser
{
    /**
     * Parse a .magicsscript file content into structured data.
     *
     * Format:
     *   Magic Solution Script
     *   ver 1.0
     *   starting:
     *   2097152              <- expected file size in bytes
     *   hex_offset new_byte original_byte
     *   dd4b6 e8 0
     *   ...
     *
     * @param string $scriptContent Raw text content of the .magicsscript file
     * @return array ['expected_size' => int, 'version' => string, 'patches' => [...]]
     * @throws \InvalidArgumentException
     */
    public function parse(string $scriptContent): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($scriptContent));

        if (count($lines) < 5) {
            throw new \InvalidArgumentException('Invalid magicsscript: file too short (minimum 5 lines required).');
        }

        // Line 1: "Magic Solution Script"
        if (trim($lines[0]) !== 'Magic Solution Script') {
            throw new \InvalidArgumentException('Invalid magicsscript: missing header "Magic Solution Script".');
        }

        // Line 2: "ver X.X"
        $version = '1.0';
        if (preg_match('/^ver\s+(.+)$/i', trim($lines[1]), $m)) {
            $version = $m[1];
        }

        // Line 3: "starting:"
        if (trim($lines[2]) !== 'starting:') {
            throw new \InvalidArgumentException('Invalid magicsscript: missing "starting:" delimiter on line 3.');
        }

        // Line 4: expected file size (decimal)
        $expectedSize = intval(trim($lines[3]));
        if ($expectedSize <= 0) {
            throw new \InvalidArgumentException('Invalid magicsscript: invalid file size on line 4.');
        }

        // Lines 5+: patch instructions
        $patches = [];
        for ($i = 4; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if ($line === '') {
                continue;
            }

            $parts = preg_split('/\s+/', $line);
            if (count($parts) < 2) {
                continue; // skip malformed lines
            }

            $offset = hexdec($parts[0]);
            $newByte = hexdec($parts[1]);
            $originalByte = isset($parts[2]) ? hexdec($parts[2]) : null;

            // Validate byte values (0-255)
            if ($newByte < 0 || $newByte > 255) {
                continue;
            }

            $patches[] = [
                'offset' => $offset,
                'new' => $newByte,
                'original' => $originalByte,
                'line' => $i + 1, // 1-based line number for error reporting
            ];
        }

        if (empty($patches)) {
            throw new \InvalidArgumentException('Invalid magicsscript: no valid patch instructions found.');
        }

        return [
            'version' => $version,
            'expected_size' => $expectedSize,
            'patches' => $patches,
            'patch_count' => count($patches),
        ];
    }

    /**
     * Validate that a script content is parseable without applying it.
     *
     * @param string $scriptContent
     * @return array ['valid' => bool, 'error' => string|null, 'patch_count' => int]
     */
    public function validate(string $scriptContent): array
    {
        try {
            $parsed = $this->parse($scriptContent);
            return [
                'valid' => true,
                'error' => null,
                'patch_count' => $parsed['patch_count'],
                'expected_size' => $parsed['expected_size'],
            ];
        } catch (\InvalidArgumentException $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage(),
                'patch_count' => 0,
                'expected_size' => 0,
            ];
        }
    }
}
