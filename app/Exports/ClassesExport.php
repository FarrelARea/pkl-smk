<?php

namespace App\Exports;

use App\Models\SchoolClass;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassesExport implements FromCollection, WithHeadings
{
    public function collection(): Enumerable
    {
        return SchoolClass::with('school')->get()->map(function ($class) {
            return [
                'Nama Sekolah' => $class->school->name ?? '',
                'Nama' => $class->name,
                'Tahun Ajaran' => $class->academic_year,
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama Sekolah', 'Nama', 'Tahun Ajaran'];
    }
}
