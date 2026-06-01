<?php

namespace App\Services;

use App\Models\ECUFileRecord;
use App\Models\EcuSignature;

class EcuDetectionService
{
    /**
     * Detect ECU type from binary file content.
     *
     * Strategy:
     * 1. Filter signatures by file size
     * 2. For each candidate, verify signature bytes at the given offset
     * 3. Return the first match with full car details
     *
     * @param string $fileContent Raw binary content of the uploaded ECU file
     * @return array|null [
     *   'ecu_uuid' => string,
     *   'ecu_file_uuid' => string|null,
     *   'car_make' => string,
     *   'car_model' => string,
     *   'year_range' => string,
     *   'ecu_type' => string,
     *   'hw_sw_number' => string,
     *   'signature' => EcuSignature,
     * ]
     */
    public function detect(string $fileContent): ?array
    {
        $fileSize = strlen($fileContent);

        // Step 1: Get all signatures matching this file size
        $candidates = EcuSignature::where('file_size', $fileSize)->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        // Step 2: Try to match signature bytes
        foreach ($candidates as $signature) {
            if ($this->matchesSignature($fileContent, $signature)) {
                return $this->buildResult($signature);
            }
        }

        // Step 3: If no signature bytes match but we have size-only candidates,
        // return the first one as a "possible" match
        $sizeOnlyCandidates = $candidates->whereNull('signature_bytes');
        if ($sizeOnlyCandidates->isNotEmpty()) {
            $best = $sizeOnlyCandidates->first();
            $result = $this->buildResult($best);
            $result['confidence'] = 'low';
            return $result;
        }

        return null;
    }

    /**
     * Fallback detection: scan ECUFileRecord scripts for expected_size match.
     * Used when no ecu_signatures exist in DB yet.
     */
    public function detectFromScripts(string $fileContent): ?array
    {
        $fileSize = strlen($fileContent);

        $records = ECUFileRecord::where('patch_method', 'script')
            ->whereNotNull('script_content')
            ->with(['ecu_file.ecu', 'module'])
            ->get();

        foreach ($records as $record) {
            // Parse expected_size from script header
            $expectedSize = $this->getScriptExpectedSize($record->script_content);
            if ($expectedSize !== null && $expectedSize === $fileSize) {
                $ecu     = optional(optional($record->ecu_file)->ecu);
                $ecuFile = optional($record->ecu_file);
                return [
                    'ecu_uuid'      => $ecuFile->ecu_uuid ?? null,
                    'ecu_file_uuid' => $ecuFile->uuid ?? null,
                    'car_make'      => $ecu->brand_name ?? ($record->module_name ?? 'Unknown'),
                    'car_model'     => $ecu->ecu_name ?? 'Unknown',
                    'year_range'    => null,
                    'ecu_type'      => null,
                    'hw_sw_number'  => null,
                    'signature'     => null,
                    'confidence'    => 'script_size_match',
                    'matched_by'    => 'script',
                ];
            }
        }

        return null;
    }

    /**
     * Extract expected file size from first lines of a .magicsscript content.
     * Format: line 4 (after header, version, empty) contains the decimal size.
     */
    protected function getScriptExpectedSize(string $scriptContent): ?int
    {
        $lines = preg_split('/\r?\n/', trim($scriptContent));
        // Look for a line that is just a number (the expected file size)
        foreach (array_slice($lines, 0, 10) as $line) {
            $trimmed = trim($line);
            if (preg_match('/^\d{4,}$/', $trimmed)) {
                return (int)$trimmed;
            }
        }
        return null;
    }

    /**
     * Check if binary content matches a signature's bytes at the given offset.
     */
    protected function matchesSignature(string $fileContent, EcuSignature $signature): bool
    {
        if (empty($signature->signature_bytes) || empty($signature->signature_offset)) {
            return false;
        }

        // Convert hex offset to integer
        $offset = $this->parseHexOffset($signature->signature_offset);
        if ($offset === false) {
            return false;
        }

        // Convert expected hex bytes to binary
        $expectedBytes = hex2bin(str_replace(' ', '', $signature->signature_bytes));
        if ($expectedBytes === false) {
            return false;
        }

        $length = strlen($expectedBytes);

        // Check if offset + length is within file bounds
        if ($offset + $length > strlen($fileContent)) {
            return false;
        }

        // Extract bytes from file at the given offset
        $actualBytes = substr($fileContent, $offset, $length);

        return $actualBytes === $expectedBytes;
    }

    /**
     * Parse a hex offset string (with or without 0x prefix) to integer.
     */
    protected function parseHexOffset(string $offset): int
    {
        $offset = trim($offset);
        if (strpos($offset, '0x') === 0 || strpos($offset, '0X') === 0) {
            return hexdec(substr($offset, 2));
        }
        // Try as hex if it contains a-f characters
        if (preg_match('/[a-fA-F]/', $offset)) {
            return hexdec($offset);
        }
        // Try as decimal
        return intval($offset);
    }

    /**
     * Build result array from a matched signature.
     */
    protected function buildResult(EcuSignature $signature): array
    {
        return [
            'ecu_uuid' => $signature->ecu_uuid,
            'ecu_file_uuid' => $signature->ecu_file_uuid,
            'car_make' => $signature->car_make,
            'car_model' => $signature->car_model,
            'year_range' => $signature->year_range,
            'ecu_type' => $signature->ecu_type,
            'hw_sw_number' => $signature->hw_sw_number,
            'signature' => $signature,
            'confidence' => 'high',
        ];
    }

    /**
     * Get available modifications for a detected ECU.
     *
     * @param string $ecuUuid
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableModifications(string $ecuUuid): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\ECUFileRecord::whereHas('ecu_file', function ($q) use ($ecuUuid) {
            $q->where('ecu_uuid', $ecuUuid);
        })->where('patch_method', 'script')
          ->whereNotNull('script_content')
          ->with('module')
          ->get();
    }
}
