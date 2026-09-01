<?php

namespace App\Exports;

use App\Models\AttendancePoint;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendancePointsExport implements FromCollection, WithHeadings
{
    public function collection(): Enumerable
    {
        return AttendancePoint::with(['company', 'company.school'])->get()->map(function ($point) {
            return [
                'Nama Sekolah' => $point->company->school->name ?? '',
                'Nama Perusahaan' => $point->company->name ?? '',
                'Nama' => $point->name,
                'Latitude' => $point->latitude,
                'Longitude' => $point->longitude,
                'Batas Jarak' => $point->distance_threshold,
                'Status' => $point->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Sekolah',
            'Nama Perusahaan',
            'Nama',
            'Latitude',
            'Longitude',
            'Batas Jarak',
            'Status',
        ];
    }
}
