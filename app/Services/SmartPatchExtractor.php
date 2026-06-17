<?php

namespace App\Services;

/**
 * SmartPatchExtractor
 *
 * Takes 3 binary strings:
 *   - $ori1  : original file from car A (the car you tuned)
 *   - $mod   : fixed/tuned version of car A
 *   - $ori2  : original file from car B (same ECU, different VIN/immo)
 *
 * Produces a patch_map JSON that can safely be applied to any car
 * sharing the same ECU, with wildcards (??) masking bytes that differ
 * between individual cars (VIN, immo, checksums).
 */
class SmartPatchExtractor
{
    private int $contextSize  = 16;   // bytes of context before + after each cluster
    private int $gapTolerance = 30;   // max gap (bytes) between changed offsets in same cluster

    public function __construct(int $contextSize = 16, int $gapTolerance = 30)
    {
        $this->contextSize  = $contextSize;
        $this->gapTolerance = $gapTolerance;
    }

    /**
     * Main entry point.
     *
     * @throws \InvalidArgumentException if file sizes do not match
     */
    public function extract(string $ori1, string $mod, string $ori2): array
    {
        $size = strlen($ori1);

        if (strlen($mod) !== $size || strlen($ori2) !== $size) {
            throw new \InvalidArgumentException(
                "All three files must be the same size. " .
                "ori1={$size}, mod=" . strlen($mod) . ", ori2=" . strlen($ori2)
            );
        }

        // Step 1 — build a compact bitset: bit N is set when ori1[N] !== ori2[N]
        // Uses ceil(size/8) bytes instead of a PHP hash-map (~50 bytes per entry).
        $variableBitset = $this->buildVariableBitset($ori1, $ori2);

        // Step 2 — collect changed offsets and cluster them in a single pass
        // to avoid allocating a second large intermediate array.
        $patchClusters  = [];
        $totalWildcards = 0;
        $changedTotal   = 0;
        $currentCluster = null;   // ['start', 'end']

        $flushCluster = function () use (
            &$currentCluster, &$patchClusters, &$totalWildcards,
            $ori1, $mod, $variableBitset, $size
        ) {
            if ($currentCluster === null) {
                return;
            }

            $start   = max(0, $currentCluster['start'] - $this->contextSize);
            $end     = min($size - 1, $currentCluster['end'] + $this->contextSize);
            $search  = [];
            $replace = [];

            for ($i = $start; $i <= $end; $i++) {
                $isWildcard = (bool) (ord($variableBitset[$i >> 3]) & (1 << ($i & 7)));

                if ($isWildcard) {
                    $search[]  = '??';
                    $replace[] = '??';
                    $totalWildcards++;
                } else {
                    $search[]  = strtoupper(bin2hex($ori1[$i]));
                    $replace[] = strtoupper(bin2hex($mod[$i]));
                }
            }

            $patchClusters[] = [
                'offset_start' => $start,
                'offset_end'   => $end,
                'search'       => $search,
                'replace'      => $replace,
            ];

            $currentCluster = null;
        };

        for ($i = 0; $i < $size; $i++) {
            if ($ori1[$i] === $mod[$i]) {
                continue;
            }

            $changedTotal++;

            if ($currentCluster === null) {
                $currentCluster = ['start' => $i, 'end' => $i];
            } elseif ($i - $currentCluster['end'] <= $this->gapTolerance) {
                $currentCluster['end'] = $i;
            } else {
                $flushCluster();
                $currentCluster = ['start' => $i, 'end' => $i];
            }
        }

        $flushCluster();

        if ($changedTotal === 0) {
            throw new \InvalidArgumentException('ori1 and mod are identical — no patch to extract.');
        }

        return [
            'file_size'            => $size,
            'ecu_software_number'  => $this->extractEcuSoftwareNumber($ori1),
            'patches_count'        => $changedTotal,
            'wildcard_count'       => $totalWildcards,
            'clusters'             => $patchClusters,
        ];
    }

    public function getContextSize(): int  { return $this->contextSize; }
    public function getGapTolerance(): int { return $this->gapTolerance; }

    /**
     * Extract the ECU Software Number from a binary file.
     *
     * Scans the first 512 bytes for the first contiguous printable ASCII string
     * that is ≥ 10 characters long — this is the ECU calibration identifier
     * (e.g. "10375575171200E00I" for Bosch EDC17C57).
     *
     * The identifier sits at offset 0x1a in Bosch files but scanning makes the
     * method work across ECU families without hard-coding offsets.
     */
    public function extractEcuSoftwareNumber(string $binary): ?string
    {
        $header = substr($binary, 0, 512);
        $result = '';

        for ($i = 0; $i < strlen($header); $i++) {
            $byte = ord($header[$i]);
            if ($byte >= 0x20 && $byte <= 0x7E) {
                $result .= $header[$i];
            } else {
                if (strlen(trim($result)) >= 10) {
                    return trim($result);
                }
                $result = '';
            }
        }

        $trimmed = trim($result);
        return strlen($trimmed) >= 10 ? $trimmed : null;
    }

    /**
     * Build a compact bitset for variable offsets.
     *
     * Each bit N is 1 when ori1[N] !== ori2[N].
     * Memory: ceil(fileSize / 8) bytes  →  512 KB for a 4 MB file
     * vs. PHP associative array:         →  ~200 MB for the same file.
     */
    private function buildVariableBitset(string $ori1, string $ori2): string
    {
        $len    = strlen($ori1);
        $bitset = str_repeat("\0", (int) ceil($len / 8));

        for ($i = 0; $i < $len; $i++) {
            if ($ori1[$i] !== $ori2[$i]) {
                $bitset[$i >> 3] = chr(ord($bitset[$i >> 3]) | (1 << ($i & 7)));
            }
        }

        return $bitset;
    }
}
