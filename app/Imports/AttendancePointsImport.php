<?php

namespace App\Imports;

use App\Models\AttendancePoint;
use App\Models\Company;
use App\Models\School;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AttendancePointsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $school = School::firstOrCreate(
            ['name' => $row['nama_sekolah']]
        );

        $company = Company::firstOrCreate(
            [
                'name' => $row['nama_perusahaan'],
                'school_id' => $school->id,
            ]
        );

        return AttendancePoint::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => $row['nama'],
            ],
            [
                'school_id' => $school->id,
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'distance_threshold' => $row['batas_jarak'] ?? 100,
                'status' => $row['status'] ?? 'approved',
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nama_sekolah' => 'required|string',
            'nama_perusahaan' => 'required|string',
            'nama' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }
}
