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

        // Step 1 — variable offsets (bytes that differ between two stock files)
        $variableOffsets = $this->findVariableOffsets($ori1, $ori2);

        // Step 2 — changed offsets (actual fix: ori1 → mod)
        $changedOffsets = $this->findChangedOffsets($ori1, $mod);

        if (empty($changedOffsets)) {
            throw new \InvalidArgumentException('ori1 and mod are identical — no patch to extract.');
        }

        // Step 3 — cluster nearby changed offsets together
        $clusters = $this->clusterOffsets($changedOffsets);

        // Step 4 — build search/replace masks with context + wildcards
        $patchClusters  = [];
        $totalWildcards = 0;

        foreach ($clusters as $cluster) {
            $start = max(0, $cluster['start'] - $this->contextSize);
            $end   = min($size - 1, $cluster['end'] + $this->contextSize);

            $search  = [];
            $replace = [];

            for ($i = $start; $i <= $end; $i++) {
                $isWildcard = isset($variableOffsets[$i]);

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
        }

        return [
            'file_size'      => $size,
            'patches_count'  => count($changedOffsets),
            'wildcard_count' => $totalWildcards,
            'clusters'       => $patchClusters,
        ];
    }

    public function getContextSize(): int  { return $this->contextSize; }
    public function getGapTolerance(): int { return $this->gapTolerance; }

    /** Returns map of offset => true for bytes that differ between ori1 and ori2 */
    private function findVariableOffsets(string $ori1, string $ori2): array
    {
        $result = [];
        $len    = strlen($ori1);

        for ($i = 0; $i < $len; $i++) {
            if ($ori1[$i] !== $ori2[$i]) {
                $result[$i] = true;
            }
        }

        return $result;
    }

    /** Returns list of offsets where ori1 and mod differ */
    private function findChangedOffsets(string $ori1, string $mod): array
    {
        $result = [];
        $len    = strlen($ori1);

        for ($i = 0; $i < $len; $i++) {
            if ($ori1[$i] !== $mod[$i]) {
                $result[] = $i;
            }
        }

        return $result;
    }

    /** Groups nearby offsets into clusters using gap tolerance */
    private function clusterOffsets(array $offsets): array
    {
        $clusters = [];
        $current  = ['start' => $offsets[0], 'end' => $offsets[0]];

        for ($i = 1; $i < count($offsets); $i++) {
            if ($offsets[$i] - $current['end'] <= $this->gapTolerance) {
                $current['end'] = $offsets[$i];
            } else {
                $clusters[] = $current;
                $current    = ['start' => $offsets[$i], 'end' => $offsets[$i]];
            }
        }

        $clusters[] = $current;

        return $clusters;
    }
}
