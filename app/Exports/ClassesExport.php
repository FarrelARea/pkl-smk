<?php

namespace App\Exports;

use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return SchoolClass::with('school')->get()->map(function ($class) {
            return [
                'name' => $class->name,
                'academic_year' => $class->academic_year,
                'school_name' => $class->school->name ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['name', 'academic_year', 'school_name'];
    }
}