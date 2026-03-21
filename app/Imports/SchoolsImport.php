<?php

namespace App\Imports;

use App\Models\School;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SchoolsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return School::updateOrCreate(
            ['name' => $row['name']],
            [
                'address' => $row['address'] ?? null,
                'phone' => $row['phone'] ?? null,
                'email' => $row['email'] ?? null,
            ]
        );
    }
}