<?php

namespace App\Exports;

use App\Models\TeacherCompanyAssignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeacherCompanyAssignmentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return TeacherCompanyAssignment::with(['teacher', 'company'])->get()->map(function ($assignment) {
            return [
                'Email Guru' => $assignment->teacher->email ?? '',
                'Nama Perusahaan' => $assignment->company->name ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['Email Guru', 'Nama Perusahaan'];
    }
}
