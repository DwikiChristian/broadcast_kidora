<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;


class UsersImport implements ToCollection, WithHeadingRow, WithStartRow
{
    public function startRow(): int
        {
            return 2;
        }
        public function collection(Collection $rows)
        {
            // Debugging: Periksa semua data sebelum diproses
            // dd($rows); 
        
            foreach ($rows as $row) {
                Log::info('Proses Baris:', $row->toArray());
        
                User::create([
                    'name'         => $row['name'] ?? null,
                    'phone_number' => $row['phone_number'] ?? null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                    'nip'          => $row['nip'] ?? null,
                ]);
            }
        }
    }
