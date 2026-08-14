<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompaniesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Company::with('school')->get()->map(function ($company) {
            return [
                'Nama Sekolah' => $company->school->name ?? '',
                'Nama' => $company->name,
                'Alamat' => $company->address,
                'Provinsi' => $company->province_name,
                'Kota' => $company->city_name,
                'Kecamatan' => $company->district_name,
                'Desa' => $company->village_name,
                'Industri' => $company->industry,
                'Telepon' => $company->phone,
                'Email' => $company->email,
                'Latitude' => $company->latitude,
                'Longitude' => $company->longitude,
                'Batas Jarak (m)' => $company->distance_threshold,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Sekolah',
            'Nama',
            'Alamat',
            'Provinsi',
            'Kota',
            'Kecamatan',
            'Desa',
            'Industri',
            'Telepon',
            'Email',
            'Latitude',
            'Longitude',
            'Batas Jarak (m)',
        ];
    }
}
