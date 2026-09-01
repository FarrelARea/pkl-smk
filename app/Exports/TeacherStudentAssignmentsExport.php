<?php

namespace App\Exports;

use App\Models\TeacherStudentAssignment;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeacherStudentAssignmentsExport implements FromCollection, WithHeadings
{
    public function collection(): Enumerable
    {
        return TeacherStudentAssignment::with(['teacher', 'student'])->get()->map(function ($assignment) {
            return [
                'Email Guru' => $assignment->teacher->email ?? '',
                'Email Siswa' => $assignment->student->email ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['Email Guru', 'Email Siswa'];
    }
}
