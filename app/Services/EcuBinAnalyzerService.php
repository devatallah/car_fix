<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EcuBinAnalyzerService
{
    /**
     * Magic byte signatures → ECU type name.
     * Each entry: [hex_needle, label]
     */
    protected array $magicBytes = [
        ['4d4537', 'Bosch ME7.x'],
        ['4d4539', 'Bosch ME9.x'],
        ['4d4544', 'Bosch MED17'],
        ['4d454417', 'Bosch MED17.x'],
        ['424d5731', 'Bosch EDC17'],
        ['5349454d454e53', 'Siemens SID'],
        ['4d41524b4c494e', 'Marelli'],
        ['44454c504849', 'Delphi'],
        ['56414c454f', 'Valeo'],
        ['44454e534f', 'Denso'],
    ];

    /**
     * File size (bytes) → fallback ECU type.
     */
    protected array $sizeMap = [
        131072  => 'Bosch ME7.x (128KB)',
        262144  => 'Bosch ME7.x (256KB)',
        524288  => 'Bosch ME9.x / MED (512KB)',
        1048576 => 'MED17 / Denso (1MB)',
        2097152 => 'MED17 / Tricore (2MB)',
    ];

    /**
     * WMI prefix (3 chars) → Car manufacturer name.
     */
    protected array $wmiMap = [
        // ── Germany ──────────────────────────────────────────
        'WBA' => 'BMW', 'WBS' => 'BMW', 'WBY' => 'BMW', 'WBX' => 'BMW',
        'WAU' => 'Audi', 'WAP' => 'Audi', 'TRU' => 'Audi',
        'WVW' => 'Volkswagen', 'WV1' => 'Volkswagen', 'WV2' => 'Volkswagen',
        'WV3' => 'Volkswagen', 'WVG' => 'Volkswagen',
        'WDB' => 'Mercedes-Benz', 'WDD' => 'Mercedes-Benz', 'WDC' => 'Mercedes-Benz',
        'WDF' => 'Mercedes-Benz', 'W1K' => 'Mercedes-Benz', 'W1N' => 'Mercedes-Benz',
        'WP0' => 'Porsche', 'WP1' => 'Porsche',
        'W0L' => 'Opel', 'W0V' => 'Opel',
        'WME' => 'Smart',
        'WF0' => 'Ford (Germany)', 'WF1' => 'Ford (Germany)',
        // ── France ──────────────────────────────────────────
        'VF1' => 'Renault', 'VF2' => 'Renault', 'VF6' => 'Renault',
        'VF3' => 'Peugeot', 'VF4' => 'Peugeot',
        'VF7' => 'Citroën', 'VF8' => 'Citroën', 'VF9' => 'Citroën',
        // ── Italy ────────────────────────────────────────────
        'ZFA' => 'Fiat', 'ZCF' => 'Fiat', 'ZFF' => 'Ferrari',
        'ZAR' => 'Alfa Romeo', 'ZLA' => 'Lancia', 'ZHW' => 'Lamborghini',
        'ZAM' => 'Maserati',
        // ── UK ──────────────────────────────────────────────
        'SAJ' => 'Jaguar', 'SAL' => 'Land Rover', 'SCB' => 'Bentley',
        'SCC' => 'Aston Martin', 'SCE' => 'Aston Martin', 'SAR' => 'Rover',
        'SFD' => 'Alexander Dennis', 'SHH' => 'Honda (UK)',
        // ── Sweden ──────────────────────────────────────────
        'YV1' => 'Volvo', 'YV4' => 'Volvo', 'YV2' => 'Volvo',
        'YS3' => 'Saab',
        // ── Japan (Toyota) ───────────────────────────────────
        'JT1' => 'Toyota', 'JT2' => 'Toyota', 'JT3' => 'Toyota',
        'JT4' => 'Toyota', 'JT5' => 'Toyota', 'JT6' => 'Toyota',
        'JT7' => 'Toyota', 'JT8' => 'Toyota', 'JT9' => 'Toyota',
        'JTD' => 'Toyota', 'JTG' => 'Toyota', 'JTJ' => 'Toyota',
        'JTK' => 'Toyota', 'JTL' => 'Toyota', 'JTM' => 'Toyota',
        'JTN' => 'Toyota', 'JTS' => 'Toyota', 'JTU' => 'Toyota',
        'JTW' => 'Toyota', 'JTX' => 'Toyota', 'JTY' => 'Toyota',
        // ── Japan (Lexus) ────────────────────────────────────
        'JTH' => 'Lexus', 'JTB' => 'Lexus', 'JTC' => 'Lexus',
        'JTE' => 'Lexus', 'JTF' => 'Lexus',
        // ── Japan (Honda) ────────────────────────────────────
        'JHM' => 'Honda', 'JH4' => 'Honda', 'JH2' => 'Honda',
        'JHL' => 'Honda',
        // ── Japan (Nissan) ───────────────────────────────────
        'JN1' => 'Nissan', 'JN6' => 'Nissan', 'JN3' => 'Nissan',
        'JNA' => 'Nissan', 'JNB' => 'Nissan', 'JNC' => 'Nissan',
        'JND' => 'Nissan', 'JNE' => 'Nissan', 'JNF' => 'Nissan',
        'JNG' => 'Nissan', 'JNK' => 'Nissan',
        // ── Japan (Mitsubishi) ───────────────────────────────
        'JA3' => 'Mitsubishi', 'JA4' => 'Mitsubishi', 'JA5' => 'Mitsubishi',
        'JA7' => 'Mitsubishi', 'JMB' => 'Mitsubishi', 'JMY' => 'Mitsubishi',
        // ── Japan (Mazda / Subaru / Suzuki / Isuzu) ─────────
        'JM1' => 'Mazda', 'JM3' => 'Mazda', 'JM6' => 'Mazda',
        'JF1' => 'Subaru', 'JF2' => 'Subaru',
        'JS1' => 'Suzuki', 'JS2' => 'Suzuki', 'JS3' => 'Suzuki',
        'JAA' => 'Isuzu', 'JAB' => 'Isuzu',
        // ── Korea ────────────────────────────────────────────
        'KNA' => 'Kia', 'KNB' => 'Kia', 'KNC' => 'Kia',
        'KND' => 'Kia', 'KNE' => 'Kia', 'KNF' => 'Kia',
        'KMH' => 'Hyundai', 'KMF' => 'Hyundai', 'KMJ' => 'Hyundai',
        'KMX' => 'Hyundai', 'KM8' => 'Hyundai',
        // ── USA ─────────────────────────────────────────────
        '1G1' => 'Chevrolet', '1G4' => 'Buick', '1GC' => 'Chevrolet',
        '1FA' => 'Ford', '1FB' => 'Ford', '1FT' => 'Ford', '1FM' => 'Ford',
        '1C3' => 'Chrysler', '1C4' => 'Jeep', '1C6' => 'Ram',
        '2T1' => 'Toyota (Canada)', '3VW' => 'Volkswagen (Mexico)',
        // ── Spain ────────────────────────────────────────────
        'VS6' => 'Ford (Spain)', 'VS7' => 'Ford (Spain)', 'VSS' => 'SEAT',
        'VSK' => 'SEAT',
        // ── Czech / Slovakia ─────────────────────────────────
        'TMB' => 'Škoda', 'TMA' => 'Škoda',
        // ── Romania ──────────────────────────────────────────
        'UU1' => 'Dacia', 'UU3' => 'Dacia',
        // ── Turkey ───────────────────────────────────────────
        'NMT' => 'Toyota (Turkey)', 'NM0' => 'Ford (Turkey)',
        'NMA' => 'Renault (Turkey)',
        // ── Netherlands ──────────────────────────────────────
        'XLE' => 'Volvo (NL)', 'XLR' => 'DAF',
        // ── Belgium ──────────────────────────────────────────
        'ZLF' => 'Volvo (Belgium)',
        // ── India ────────────────────────────────────────────
        'MA1' => 'Mahindra', 'MA3' => 'Suzuki (India)',
        'MA6' => 'GM (India)', 'MAJ' => 'Ford (India)',
        'MAT' => 'Toyota (India)',
    ];

    /**
     * Known car model name strings to search in binary.
     * Ordered from specific to general.
     */
    protected array $modelStrings = [
        // VW
        'GOLF', 'POLO', 'PASSAT', 'TIGUAN', 'TOUAREG', 'JETTA', 'CADDY', 'SHARAN', 'TRANSPORTER',
        // Audi
        'QUATTRO',
        // BMW
        '3 SERIES', '5 SERIES', '7 SERIES', 'X5', 'X3', 'X1', 'M3', 'M5',
        // Mercedes
        'C-CLASS', 'E-CLASS', 'S-CLASS', 'GLC', 'CLA', 'GLE',
        // Ford
        'FOCUS', 'FIESTA', 'MONDEO', 'TRANSIT', 'RANGER', 'KUGA', 'PUMA',
        // Opel/Vauxhall
        'ASTRA', 'CORSA', 'VECTRA', 'INSIGNIA', 'ZAFIRA', 'MOKKA',
        // Peugeot
        '206', '207', '208', '306', '307', '308', '406', '407', '408', '508', '3008', '5008',
        // Citroën
        'C3', 'C4', 'C5', 'BERLINGO', 'DISPATCH', 'JUMPER',
        // Renault
        'CLIO', 'MEGANE', 'LAGUNA', 'SCENIC', 'KANGOO', 'TRAFIC', 'MASTER',
        // Fiat
        'PUNTO', 'BRAVO', 'STILO', 'DOBLO', 'DUCATO', 'PANDA',
        // Toyota
        'COROLLA', 'CAMRY', 'YARIS', 'HILUX', 'LAND CRUISER', 'RAV4',
        // Nissan
        'QASHQAI', 'JUKE', 'NAVARA', 'PATHFINDER', 'MICRA', 'ALMERA',
        // Hyundai/Kia
        'TUCSON', 'SANTA FE', 'SPORTAGE', 'SORENTO', 'CERATO',
        // Skoda
        'OCTAVIA', 'FABIA', 'SUPERB', 'KODIAQ', 'KAROQ',
        // Seat
        'IBIZA', 'LEON', 'ALTEA', 'ALHAMBRA', 'ATECA',
        // Volvo
        'S40', 'S60', 'S80', 'V40', 'V50', 'V60', 'V70', 'XC60', 'XC90',
        // Jeep/Chrysler
        'CHEROKEE', 'WRANGLER', 'COMPASS',
        // Land Rover
        'DISCOVERY', 'DEFENDER', 'FREELANDER', 'EVOQUE',
    ];

    /**
     * Analyze a raw ECU binary and extract all available metadata.
     *
     * @param string      $binaryContent Raw bytes from the uploaded .bin file
     * @param string|null $filename      Original filename (used for brand detection)
     * @return array
     */
    public function analyze(string $binaryContent, ?string $filename = null): array
    {
        $fileSize = strlen($binaryContent);
        $errors   = [];

        $ecuType       = $this->detectEcuType($binaryContent, $fileSize);
        $vin           = $this->extractVin($binaryContent, $vinOffset);
        $strings       = $this->extractPrintableStrings($binaryContent);
        $partNumber    = $this->extractPartNumber($strings);
        $swVersion     = $this->extractSwVersion($strings);
        $hwVersion     = $this->extractHwVersion($strings);
        $calibrationId = $this->extractCalibrationId($strings);
        $checksum      = $this->calcChecksum16($binaryContent);

        // ── Layer 0: model from binary strings ──────────────────────────────
        $carModel = $this->extractCarModel($binaryContent);
        $carMake  = null;

        // ── Layer 1: NHTSA API (only when valid WMI-verified VIN found) ─────
        if ($vin) {
            $carMake = $this->decodeCarMake($vin);
            [$apiMake, $apiModel] = $this->lookupVinNhtsa($vin);
            if ($apiMake)              $carMake  = $apiMake;
            if ($apiModel && !$carModel) $carModel = $apiModel;
        }

        // ── Layer 2: original filename scan ─────────────────────────────────
        if (!$carMake && $filename) {
            $carMake = $this->extractMakeFromFilename($filename);
        }

        // ── Layer 3: model → make inference ─────────────────────────────────
        if (!$carMake && $carModel) {
            $carMake = $this->inferMakeFromModel($carModel);
        }

        // ── Layer 4: raw binary string scan ─────────────────────────────────
        if (!$carMake) {
            $carMake = $this->extractCarMakeFromStrings($binaryContent);
        }

        // Fallback car_model to ecu_type if nothing found
        $carModel = $carModel ?? $ecuType;

        Log::info('[ECU Analyzer]', [
            'filename'   => $filename,
            'file_size'  => $fileSize,
            'ecu_type'   => $ecuType,
            'vin'        => $vin,
            'vin_offset' => $vinOffset,
            'car_make'   => $carMake,
            'car_model'  => $carModel,
        ]);

        $status = ($vin || $ecuType) ? 'success' : 'partial';

        return [
            'analysis_status' => $status,
            'errors'          => $errors,
            'car_make'        => $carMake,
            'car_model'       => $carModel,
            'ecu_type'        => $ecuType,
            'vin'             => $vin,
            'vin_offset'      => $vinOffset,
            'part_number'     => $partNumber,
            'calibration_id'  => $calibrationId,
            'sw_version'      => $swVersion,
            'hw_version'      => $hwVersion,
            'file_size_bytes' => $fileSize,
            'checksum_16bit'  => $checksum,
        ];
    }

    // ─────────────────────────────────────────────
    //  Car Make — decoded from VIN WMI
    // ─────────────────────────────────────────────

    protected function decodeCarMake(string $vin): ?string
    {
        $wmi3 = strtoupper(substr($vin, 0, 3));
        return $this->wmiMap[$wmi3] ?? null;
    }

    // ─────────────────────────────────────────────
    //  Car Make — NHTSA VIN Decode API (free, no key)
    // ─────────────────────────────────────────────

    /**
     * Call the free NHTSA VIN API and return [make, model].
     * Times out after 5 seconds — never blocks the upload.
     */
    protected function lookupVinNhtsa(string $vin): array
    {
        try {
            $url      = "https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/{$vin}?format=json";
            $response = Http::timeout(5)->get($url);

            if (!$response->ok()) {
                return [null, null];
            }

            $results = collect($response->json('Results', []));

            $make  = $results->firstWhere('Variable', 'Make')['Value']  ?? null;
            $model = $results->firstWhere('Variable', 'Model')['Value'] ?? null;

            // NHTSA returns the string "null" for unknown fields
            $make  = ($make  && $make  !== 'null') ? ucwords(strtolower($make))  : null;
            $model = ($model && $model !== 'null') ? ucwords(strtolower($model)) : null;

            return [$make, $model];
        } catch (\Exception $e) {
            Log::warning('[ECU Analyzer] NHTSA lookup failed: ' . $e->getMessage());
            return [null, null];
        }
    }

    // ─────────────────────────────────────────────
    //  Car Make — filename brand scan
    // ─────────────────────────────────────────────

    protected function extractMakeFromFilename(string $filename): ?string
    {
        $upper = strtoupper($filename);

        $brands = [
            'VOLKSWAGEN' => 'Volkswagen', 'VW'         => 'Volkswagen',
            'BMW'        => 'BMW',
            'MERCEDES'   => 'Mercedes-Benz', 'BENZ'    => 'Mercedes-Benz',
            'AUDI'       => 'Audi',
            'PORSCHE'    => 'Porsche',
            'OPEL'       => 'Opel', 'VAUXHALL'         => 'Vauxhall',
            'FORD'       => 'Ford',
            'PEUGEOT'    => 'Peugeot',
            'CITROEN'    => 'Citroën', 'CITROËN'       => 'Citroën',
            'RENAULT'    => 'Renault',
            'FIAT'       => 'Fiat',
            'ALFA'       => 'Alfa Romeo',
            'SKODA'      => 'Škoda',
            'SEAT'       => 'SEAT',
            'VOLVO'      => 'Volvo',
            'TOYOTA'     => 'Toyota',
            'LEXUS'      => 'Lexus',
            'HONDA'      => 'Honda',
            'NISSAN'     => 'Nissan',
            'MITSUBISHI' => 'Mitsubishi',
            'MAZDA'      => 'Mazda',
            'SUBARU'     => 'Subaru',
            'SUZUKI'     => 'Suzuki',
            'HYUNDAI'    => 'Hyundai',
            'KIA'        => 'Kia',
            'DACIA'      => 'Dacia',
            'LAND ROVER' => 'Land Rover', 'LANDROVER'  => 'Land Rover',
            'JAGUAR'     => 'Jaguar',
            'SMART'      => 'Smart',
            'DAIHATSU'   => 'Daihatsu',
            'ISUZU'      => 'Isuzu',
        ];

        foreach ($brands as $needle => $label) {
            if (str_contains($upper, $needle)) {
                return $label;
            }
        }

        return null;
    }

    // ─────────────────────────────────────────────
    //  Car Make — inferred from detected model name
    // ─────────────────────────────────────────────

    protected function inferMakeFromModel(string $model): ?string
    {
        $map = [
            // Volkswagen
            'Golf' => 'Volkswagen', 'Polo' => 'Volkswagen', 'Passat' => 'Volkswagen',
            'Tiguan' => 'Volkswagen', 'Touareg' => 'Volkswagen', 'Jetta' => 'Volkswagen',
            'Caddy' => 'Volkswagen', 'Sharan' => 'Volkswagen', 'Transporter' => 'Volkswagen',
            // Audi
            'Quattro' => 'Audi',
            // BMW
            'X5' => 'BMW', 'X3' => 'BMW', 'X1' => 'BMW',
            'M3' => 'BMW', 'M5' => 'BMW',
            '3 Series' => 'BMW', '5 Series' => 'BMW', '7 Series' => 'BMW',
            // Mercedes-Benz
            'C-Class' => 'Mercedes-Benz', 'E-Class' => 'Mercedes-Benz',
            'S-Class' => 'Mercedes-Benz', 'Glc' => 'Mercedes-Benz',
            'Cla' => 'Mercedes-Benz', 'Gle' => 'Mercedes-Benz',
            // Ford
            'Focus' => 'Ford', 'Fiesta' => 'Ford', 'Mondeo' => 'Ford',
            'Transit' => 'Ford', 'Ranger' => 'Ford', 'Kuga' => 'Ford', 'Puma' => 'Ford',
            // Opel / Vauxhall
            'Astra' => 'Opel', 'Corsa' => 'Opel', 'Vectra' => 'Opel',
            'Insignia' => 'Opel', 'Zafira' => 'Opel', 'Mokka' => 'Opel',
            // Peugeot
            '206' => 'Peugeot', '207' => 'Peugeot', '208' => 'Peugeot',
            '306' => 'Peugeot', '307' => 'Peugeot', '308' => 'Peugeot',
            '406' => 'Peugeot', '407' => 'Peugeot', '408' => 'Peugeot',
            '508' => 'Peugeot', '3008' => 'Peugeot', '5008' => 'Peugeot',
            // Citroën
            'C3' => 'Citroën', 'C4' => 'Citroën', 'C5' => 'Citroën',
            'Berlingo' => 'Citroën', 'Dispatch' => 'Citroën', 'Jumper' => 'Citroën',
            // Renault
            'Clio' => 'Renault', 'Megane' => 'Renault', 'Laguna' => 'Renault',
            'Scenic' => 'Renault', 'Kangoo' => 'Renault',
            'Trafic' => 'Renault', 'Master' => 'Renault',
            // Fiat
            'Punto' => 'Fiat', 'Bravo' => 'Fiat', 'Stilo' => 'Fiat',
            'Doblo' => 'Fiat', 'Ducato' => 'Fiat', 'Panda' => 'Fiat',
            // Toyota
            'Corolla' => 'Toyota', 'Camry' => 'Toyota', 'Yaris' => 'Toyota',
            'Hilux' => 'Toyota', 'Land Cruiser' => 'Toyota', 'Rav4' => 'Toyota',
            // Nissan
            'Qashqai' => 'Nissan', 'Juke' => 'Nissan', 'Navara' => 'Nissan',
            'Pathfinder' => 'Nissan', 'Micra' => 'Nissan', 'Almera' => 'Nissan',
            // Hyundai / Kia
            'Tucson' => 'Hyundai', 'Santa Fe' => 'Hyundai',
            'Sportage' => 'Kia', 'Sorento' => 'Kia', 'Cerato' => 'Kia',
            // Škoda
            'Octavia' => 'Škoda', 'Fabia' => 'Škoda', 'Superb' => 'Škoda',
            'Kodiaq' => 'Škoda', 'Karoq' => 'Škoda',
            // SEAT
            'Ibiza' => 'SEAT', 'Leon' => 'SEAT', 'Altea' => 'SEAT',
            'Alhambra' => 'SEAT', 'Ateca' => 'SEAT',
            // Volvo
            'S40' => 'Volvo', 'S60' => 'Volvo', 'S80' => 'Volvo',
            'V40' => 'Volvo', 'V50' => 'Volvo', 'V60' => 'Volvo',
            'V70' => 'Volvo', 'Xc60' => 'Volvo', 'Xc90' => 'Volvo',
            // Land Rover / Jeep
            'Discovery' => 'Land Rover', 'Defender' => 'Land Rover',
            'Freelander' => 'Land Rover', 'Evoque' => 'Land Rover',
            'Cherokee' => 'Jeep', 'Wrangler' => 'Jeep', 'Compass' => 'Jeep',
        ];

        return $map[$model] ?? null;
    }

    // ─────────────────────────────────────────────
    //  Car Make — fallback: scan ASCII brand names
    // ─────────────────────────────────────────────

    protected function extractCarMakeFromStrings(string $bin): ?string
    {
        // Known brand name strings that may appear verbatim in ECU binary
        $brands = [
            'VOLKSWAGEN' => 'Volkswagen',
            'BMW'        => 'BMW',
            'MERCEDES'   => 'Mercedes-Benz',
            'AUDI'       => 'Audi',
            'PORSCHE'    => 'Porsche',
            'OPEL'       => 'Opel',
            'VAUXHALL'   => 'Vauxhall',
            'FORD'       => 'Ford',
            'PEUGEOT'    => 'Peugeot',
            'CITROEN'    => 'Citroën',
            'RENAULT'    => 'Renault',
            'FIAT'       => 'Fiat',
            'ALFA ROMEO' => 'Alfa Romeo',
            'LANCIA'     => 'Lancia',
            'SKODA'      => 'Škoda',
            'SEAT'       => 'SEAT',
            'VOLVO'      => 'Volvo',
            'SAAB'       => 'Saab',
            'JAGUAR'     => 'Jaguar',
            'LAND ROVER' => 'Land Rover',
            'ROVER'      => 'Rover',
            'TOYOTA'     => 'Toyota',
            'LEXUS'      => 'Lexus',
            'HONDA'      => 'Honda',
            'NISSAN'     => 'Nissan',
            'MITSUBISHI' => 'Mitsubishi',
            'MAZDA'      => 'Mazda',
            'SUBARU'     => 'Subaru',
            'SUZUKI'     => 'Suzuki',
            'ISUZU'      => 'Isuzu',
            'HYUNDAI'    => 'Hyundai',
            'KIA'        => 'Kia',
            'DACIA'      => 'Dacia',
            'SMART'      => 'Smart',
            'DAIHATSU'   => 'Daihatsu',
        ];

        // Scan first 256KB — brand strings are usually in the header/calibration region
        $sample = strtoupper(substr($bin, 0, 262144));

        foreach ($brands as $needle => $label) {
            if (str_contains($sample, $needle)) {
                return $label;
            }
        }

        return null;
    }

    // ─────────────────────────────────────────────
    //  Car Model — scanned from binary ASCII strings
    // ─────────────────────────────────────────────

    protected function extractCarModel(string $bin): ?string
    {
        // Scan first 128KB only
        $sample = strtoupper(substr($bin, 0, 131072));

        foreach ($this->modelStrings as $model) {
            if (str_contains($sample, $model)) {
                return ucwords(strtolower($model));
            }
        }

        return null;
    }

    // ─────────────────────────────────────────────
    //  ECU Type Detection
    // ─────────────────────────────────────────────

    protected function detectEcuType(string $bin, int $fileSize): ?string
    {
        // Scan first 512 bytes for magic byte sequences
        $headerHex = bin2hex(substr($bin, 0, 512));

        foreach ($this->magicBytes as [$needle, $label]) {
            if (str_contains($headerHex, $needle)) {
                return $label;
            }
        }

        // Scan entire file for well-known ASCII identifiers
        $knownStrings = [
            'Bosch ME7'  => 'Bosch ME7.x',
            'Bosch ME9'  => 'Bosch ME9.x',
            'MED17'      => 'Bosch MED17',
            'EDC17'      => 'Bosch EDC17',
            'Siemens'    => 'Siemens SID',
            'SIEMENS'    => 'Siemens SID',
            'Marelli'    => 'Marelli',
            'MARELLI'    => 'Marelli',
            'Delphi'     => 'Delphi',
            'DELPHI'     => 'Delphi',
            'Denso'      => 'Denso',
            'DENSO'      => 'Denso',
            'Valeo'      => 'Valeo',
            'VALEO'      => 'Valeo',
        ];

        foreach ($knownStrings as $needle => $label) {
            if (str_contains($bin, $needle)) {
                return $label;
            }
        }

        // Fallback: guess from file size
        return $this->sizeMap[$fileSize] ?? null;
    }

    // ─────────────────────────────────────────────
    //  VIN Extraction
    // ─────────────────────────────────────────────

    protected function extractVin(string $bin, ?string &$offsetHex): ?string
    {
        $offsetHex = null;
        // VIN pattern: 17 chars, uppercase letters (no I/O/Q) and digits
        $pattern = '/[A-HJ-NPR-Z0-9]{17}/';

        if (!preg_match_all($pattern, $bin, $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        foreach ($matches[0] as [$candidate, $offset]) {
            // Rule 1: first character must be a letter — VINs always start with country code (A-Z)
            if (ctype_digit($candidate[0])) {
                continue;
            }
            // Rule 2: reject if all digits (no letters at all)
            if (ctype_digit($candidate)) {
                continue;
            }
            // Rule 3: reject if unique character count is too low (e.g. FFFFFFFFFFFFF or 97666PPPPPPPP)
            if (count(array_unique(str_split($candidate))) <= 3) {
                continue;
            }
            // Rule 4: reject if last 8 or more characters are all the same (trailing junk)
            $tail = substr($candidate, -8);
            if (count(array_unique(str_split($tail))) === 1) {
                continue;
            }
            // Rule 5: WMI must be in our known manufacturer list — strongest filter
            $wmi = substr($candidate, 0, 3);
            if (!isset($this->wmiMap[$wmi])) {
                continue;
            }
            $offsetHex = '0x' . strtoupper(dechex($offset));
            return $candidate;
        }

        return null;
    }

    // ─────────────────────────────────────────────
    //  Printable String Scanner
    // ─────────────────────────────────────────────

    protected function extractPrintableStrings(string $bin, int $minLen = 6, int $maxResults = 300): array
    {
        // Scan only first 64KB — metadata is always at the header region
        $sample = substr($bin, 0, 65536);

        preg_match_all('/[\x20-\x7E]{' . $minLen . ',}/', $sample, $matches, PREG_OFFSET_CAPTURE);

        $results = [];
        foreach (array_slice($matches[0], 0, $maxResults) as [$value, $offset]) {
            $results[] = [
                'offset' => '0x' . strtoupper(dechex($offset)),
                'value'  => trim($value),
            ];
        }

        return $results;
    }

    // ─────────────────────────────────────────────
    //  Field Extractors (run on printable strings)
    // ─────────────────────────────────────────────

    protected function extractSwVersion(array $strings): ?string
    {
        $patterns = [
            '/\bSW[_\-\s]?(\d[\d\.]{2,9})\b/i',
            '/\bSOFTWARE[_\-\s]?VERSION[_\-\s]?([\d\.]{3,12})\b/i',
            '/\bV(\d+\.\d+[\.\d]*)\b/',
            '/\b(\d{4}\.\d{2,4})\b/',
        ];

        return $this->firstMatch($strings, $patterns);
    }

    protected function extractHwVersion(array $strings): ?string
    {
        $patterns = [
            '/\bHW[_\-\s]?(\w[\w\.]{2,9})\b/i',
            '/\bH\/W[_\-\s]?([\d\.]{3,10})\b/i',
            '/\bHARDWARE[_\-\s]?VERSION[_\-\s]?([\d\.]{3,12})\b/i',
        ];

        return $this->firstMatch($strings, $patterns);
    }

    protected function extractPartNumber(array $strings): ?string
    {
        $patterns = [
            '/\b(\d{10})\b/',
            '/\b(\d{7,9})\b/',
            '/\b([A-Z]{2,3}\d{5,8})\b/',
            '/\b(0261[A-Z\d]{4,8})\b/i',
        ];

        return $this->firstMatch($strings, $patterns);
    }

    protected function extractCalibrationId(array $strings): ?string
    {
        $patterns = [
            '/\b([A-Z]{2,6}_[A-Z0-9_]{5,18})\b/',
            '/\b([A-Z0-9]{8,20})\b/',
        ];

        return $this->firstMatch($strings, $patterns);
    }

    // ─────────────────────────────────────────────
    //  Checksum
    // ─────────────────────────────────────────────

    protected function calcChecksum16(string $bin): string
    {
        $sum = 0;
        $len = strlen($bin);
        for ($i = 0; $i < $len; $i++) {
            $sum += ord($bin[$i]);
        }

        return '0x' . strtoupper(sprintf('%04X', $sum & 0xFFFF));
    }

    // ─────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────

    protected function firstMatch(array $strings, array $patterns): ?string
    {
        foreach ($strings as $entry) {
            $text = $entry['value'];
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $text, $m)) {
                    return trim($m[1] ?? $m[0]);
                }
            }
        }

        return null;
    }
}
