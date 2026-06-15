<?php

namespace App\Services;

use App\Models\Internship;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InternshipImportService
{
    /**
     * Import internships from a CSV file.
     */
    public function importFromCsv(string $filePath, int $companyId): array
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return ['success' => false, 'message' => 'Could not open file.'];
        }

        $header = fgetcsv($handle, 1000, ',');
        if (! $header) {
            fclose($handle);

            return ['success' => false, 'message' => 'Empty or invalid CSV file.'];
        }

        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $results = [
            'total' => 0,
            'imported' => 0,
            'errors' => [],
        ];

        $rowNumber = 2;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $results['total']++;
            $row = array_combine($header, $data);

            $validator = Validator::make($row, [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'type' => 'required|in:full-time,part-time,remote,contract',
                'location' => 'required|string',
                'deadline_at' => 'required|date|after:today',
            ]);

            if ($validator->fails()) {
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'title' => $row['title'] ?? 'Unknown',
                    'messages' => $validator->errors()->all(),
                ];
                $rowNumber++;

                continue;
            }

            try {
                Internship::create([
                    'company_id' => $companyId,
                    'title' => $row['title'],
                    'slug' => Str::slug($row['title']).'-'.Str::random(5),
                    'description' => $row['description'],
                    'type' => $row['type'],
                    'location' => $row['location'],
                    'deadline_at' => $row['deadline_at'],
                    'requirements' => isset($row['requirements']) ? explode('|', $row['requirements']) : [],
                    'status' => 'published',
                    'is_paid' => isset($row['is_paid']) ? (bool) $row['is_paid'] : false,
                ]);

                $results['imported']++;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'title' => $row['title'] ?? 'Unknown',
                    'messages' => [$e->getMessage()],
                ];
            }

            $rowNumber++;
        }

        fclose($handle);

        return [
            'success' => true,
            'summary' => $results,
        ];
    }
}
