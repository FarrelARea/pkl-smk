<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\School;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CompaniesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): Model|array|null
    {
        $school = School::firstOrCreate(
            ['name' => $row['nama_sekolah']]
        );

        return Company::updateOrCreate(
            [
                'name' => $row['nama'],
                'school_id' => $school->id,
            ],
            [
                'address' => $row['alamat'] ?? null,
                'province_name' => $row['provinsi'] ?? null,
                'city_name' => $row['kota'] ?? null,
                'district_name' => $row['kecamatan'] ?? null,
                'village_name' => $row['desa'] ?? null,
                'industry' => $row['industri'] ?? null,
                'phone' => $row['telepon'] ?? null,
                'email' => $row['email'] ?? null,
                'latitude' => $row['latitude'] ?? null,
                'longitude' => $row['longitude'] ?? null,
                'distance_threshold' => $row['batas_jarak'] ?? 100,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nama_sekolah' => 'required|string',
            'nama' => 'required|string',
        ];
    }
}
