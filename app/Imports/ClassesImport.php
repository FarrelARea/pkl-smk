<?php

namespace App\Imports;

use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClassesImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Model|array|null
    {
        $school = School::firstOrCreate(
            ['name' => $row['nama_sekolah']]
        );

        return SchoolClass::updateOrCreate(
            [
                'name' => $row['nama'],
                'school_id' => $school->id,
            ],
            [
                'academic_year' => $row['tahun_ajaran'] ?? null,
            ]
        );
    }
}
