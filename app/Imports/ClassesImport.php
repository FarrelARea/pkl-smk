<?php

namespace App\Imports;

use App\Models\School;
use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClassesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $school = School::where('name', $row['school_name'])->first();
        
        if (!$school) {
            $school = School::create(['name' => $row['school_name']]);
        }

        return SchoolClass::updateOrCreate(
            [
                'name' => $row['name'],
                'school_id' => $school->id,
            ],
            [
                'academic_year' => $row['academic_year'] ?? null,
            ]
        );
    }
}