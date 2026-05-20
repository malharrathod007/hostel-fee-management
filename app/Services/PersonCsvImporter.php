<?php

namespace App\Services;

use App\Models\Person;
use App\Models\Room;
use Illuminate\Support\Carbon;

class PersonCsvImporter
{
    // Canonical column names (lowercase, trimmed). Each entry lists accepted aliases.
    private const COLUMN_MAP = [
        'name'           => ['name', 'full name', 'person name'],
        'phone'          => ['phone', 'mobile', 'mobile number', 'phone number', 'contact'],
        'aadhar_number'  => ['aadhar number', 'aadhar', 'aadhar no', 'aadhaar', 'aadhaar number'],
        'room_number'    => ['room number', 'room', 'room no'],
        'person_type'    => ['type', 'person type', 'category'],
        'email'          => ['email', 'email address', 'e-mail'],
        'city'           => ['city'],
        'address'        => ['address'],
        'guardian_name'  => ['guardian name', 'guardian', 'parent name'],
        'guardian_phone' => ['guardian phone', 'guardian mobile', 'parent phone'],
        'join_date'      => ['join date', 'joining date', 'date of joining', 'doj'],
        'deposit'        => ['deposit', 'deposit amount', 'security deposit'],
        'is_active'      => ['status', 'is active', 'active'],
        'notes'          => ['notes', 'remarks', 'note'],
    ];

    /**
     * Parse an uploaded CSV file and return [ 'created'=>[], 'updated'=>[], 'skipped'=>[] ]
     * where each item is ['row' => N, 'name' => '...', 'reason' => '...']
     */
    public function import(string $filePath): array
    {
        $results = ['created' => [], 'updated' => [], 'skipped' => []];

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException('Could not open the uploaded file.');
        }

        // Read BOM if present (Excel saves UTF-8 with BOM)
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Read header row
        $rawHeaders = fgetcsv($handle);
        if (!$rawHeaders) {
            fclose($handle);
            throw new \RuntimeException('The file appears to be empty or has no header row.');
        }

        $headerMap = $this->mapHeaders($rawHeaders);

        // Validate that required columns are present
        $required = ['name', 'phone', 'aadhar_number', 'room_number'];
        $missing = array_filter($required, fn($col) => !isset($headerMap[$col]));
        if ($missing) {
            fclose($handle);
            throw new \RuntimeException(
                'Missing required columns: ' . implode(', ', array_map('ucwords', str_replace('_', ' ', $missing))) . '. Please use the template provided.'
            );
        }

        // Cache rooms by room_number for performance
        $roomsCache = Room::all()->keyBy(fn($r) => strtolower(trim($r->room_number)));

        $rowNumber = 1; // header was row 1
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip completely blank rows
            if (empty(array_filter($row, fn($v) => trim($v) !== ''))) {
                continue;
            }

            $data = $this->extractRow($row, $headerMap);
            $result = $this->processRow($data, $rowNumber, $roomsCache);

            if ($result['status'] === 'created')  $results['created'][] = $result;
            elseif ($result['status'] === 'updated') $results['updated'][] = $result;
            else                                     $results['skipped'][] = $result;
        }

        fclose($handle);
        return $results;
    }

    /**
     * Build a canonical column → CSV index map from raw header row.
     */
    private function mapHeaders(array $rawHeaders): array
    {
        $map = [];
        foreach ($rawHeaders as $idx => $header) {
            $clean = strtolower(trim($header));
            foreach (self::COLUMN_MAP as $canonical => $aliases) {
                if (in_array($clean, $aliases, true)) {
                    $map[$canonical] = $idx;
                    break;
                }
            }
        }
        return $map;
    }

    /**
     * Pull a keyed array from a CSV row using the header map.
     */
    private function extractRow(array $row, array $headerMap): array
    {
        $data = [];
        foreach ($headerMap as $canonical => $idx) {
            $data[$canonical] = isset($row[$idx]) ? trim($row[$idx]) : '';
        }
        return $data;
    }

    /**
     * Validate and upsert a single row. Returns a result array.
     */
    private function processRow(array $data, int $rowNumber, $roomsCache): array
    {
        $name        = $data['name'] ?? '';
        $phone       = $data['phone'] ?? '';
        $aadhar      = $data['aadhar_number'] ?? '';
        $roomNumber  = $data['room_number'] ?? '';

        $label = $name ?: "Row {$rowNumber}";

        // ── Basic validation ──────────────────────────────────────────
        if ($name === '') {
            return $this->skip($rowNumber, $label, 'Name is required.');
        }

        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) !== 10 || !preg_match('/^[6-9]/', $phone)) {
            return $this->skip($rowNumber, $label, 'Invalid phone number (must be 10 digits, starting 6-9).');
        }

        $aadhar = preg_replace('/\D/', '', $aadhar);
        if (strlen($aadhar) !== 12) {
            return $this->skip($rowNumber, $label, 'Invalid Aadhar number (must be 12 digits).');
        }

        // ── Find room ─────────────────────────────────────────────────
        $roomKey = strtolower(trim($roomNumber));
        if (!isset($roomsCache[$roomKey])) {
            return $this->skip($rowNumber, $label, "Room \"{$roomNumber}\" not found.");
        }
        $room = $roomsCache[$roomKey];

        // ── Resolve person type ───────────────────────────────────────
        $type = strtolower($data['person_type'] ?? 'student');
        if (!in_array($type, ['student', 'employee'])) {
            $type = 'student';
        }

        // ── Join date ─────────────────────────────────────────────────
        $joinDate = null;
        if (!empty($data['join_date'])) {
            try {
                $joinDate = Carbon::parse($data['join_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $joinDate = null;
            }
        }
        $joinDate = $joinDate ?? now()->format('Y-m-d');

        // ── Status ────────────────────────────────────────────────────
        $rawStatus = strtolower($data['is_active'] ?? '');
        $isActive  = !in_array($rawStatus, ['0', 'false', 'inactive', 'no'], true);

        // ── Deposit ───────────────────────────────────────────────────
        $deposit = is_numeric($data['deposit'] ?? '') ? (float)($data['deposit']) : null;

        // ── Find existing person by Aadhar ────────────────────────────
        $existing = Person::where('aadhar_number', $aadhar)->first();

        $payload = [
            'name'           => $name,
            'phone'          => $phone,
            'aadhar_number'  => $aadhar,
            'room_id'        => $room->id,
            'person_type'    => $type,
            'email'          => $data['email'] ?: null,
            'city'           => $data['city'] ?: null,
            'address'        => $data['address'] ?: null,
            'guardian_name'  => $data['guardian_name'] ?: null,
            'guardian_phone' => $data['guardian_phone'] ? preg_replace('/\D/', '', $data['guardian_phone']) ?: null : null,
            'join_date'      => $joinDate,
            'deposit'        => $deposit,
            'is_active'      => $isActive,
            'notes'          => $data['notes'] ?: null,
        ];

        if ($existing) {
            // UPDATE
            $existing->update($payload);
            return [
                'status'     => 'updated',
                'row'        => $rowNumber,
                'name'       => $name,
                'aadhar'     => $aadhar,
            ];
        }

        // CREATE — check room capacity first
        $occupancy = $room->persons()->where('is_active', true)->count();
        if ($isActive && $occupancy >= $room->capacity) {
            return $this->skip($rowNumber, $label, "Room {$roomNumber} is full (capacity {$room->capacity}).");
        }

        // Check unique phone/email conflicts
        if (Person::where('phone', $phone)->exists()) {
            return $this->skip($rowNumber, $label, "Phone {$phone} already belongs to another person.");
        }
        if (!empty($payload['email']) && Person::where('email', $payload['email'])->exists()) {
            return $this->skip($rowNumber, $label, "Email {$payload['email']} already belongs to another person.");
        }

        Person::create($payload);
        return [
            'status' => 'created',
            'row'    => $rowNumber,
            'name'   => $name,
            'aadhar' => $aadhar,
        ];
    }

    private function skip(int $row, string $name, string $reason): array
    {
        return ['status' => 'skipped', 'row' => $row, 'name' => $name, 'reason' => $reason];
    }

    /**
     * Generate a CSV template string with headers + one example row.
     */
    public static function templateCsv(): string
    {
        $headers = [
            'Name', 'Phone', 'Aadhar Number', 'Room Number', 'Type',
            'Email', 'City', 'Address', 'Guardian Name', 'Guardian Phone',
            'Join Date', 'Deposit', 'Status', 'Notes',
        ];
        $example = [
            'Rahul Sharma', '9876543210', '123456789012', '101', 'student',
            'rahul@example.com', 'Pune', '12 MG Road', 'Ramesh Sharma', '9898989898',
            date('Y-m-d'), '5000', 'active', 'Sample row — delete before importing',
        ];

        $out = '';
        $out .= implode(',', $headers) . "\n";
        $out .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $example)) . "\n";
        return $out;
    }
}
