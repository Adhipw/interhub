<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserImportService
{
    /**
     * Import users from a CSV file.
     *
     * @return array Result summary
     */
    public function importFromCsv(string $filePath): array
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

        // Standardize header
        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $results = [
            'total' => 0,
            'imported' => 0,
            'errors' => [],
        ];

        $rowNumber = 2; // Starting after header
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $results['total']++;
            $row = array_combine($header, $data);

            $validator = Validator::make($row, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|in:student,mentor,hr,admin,super_admin',
                'phone_number' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'email' => $row['email'] ?? 'Unknown',
                    'messages' => $validator->errors()->all(),
                ];
                $rowNumber++;

                continue;
            }

            try {
                $password = Str::random(10);
                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => Hash::make($password),
                    'phone_number' => $row['phone_number'] ?? null,
                    'role' => $row['role'],
                    'is_active' => true,
                ]);

                $user->assignRole($row['role']);
                $user->markEmailAsVerified();

                $results['imported']++;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'email' => $row['email'] ?? 'Unknown',
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
