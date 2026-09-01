<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    public function collection(): Enumerable
    {
        return User::where('role', 'student')->with(['school', 'classes'])->get()->map(function ($student) {
            $classNames = $student->classes->pluck('name')->join(', ');
            $academicYear = $student->classes->pluck('academic_year')->filter()->first() ?? '';
            return [
                'Nama Sekolah' => $student->school->name ?? '',
                'Nama' => $student->name,
                'Email' => $student->email,
                'Nama Kelas' => $classNames,
                'Tahun Ajaran' => $academicYear,
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama Sekolah', 'Nama', 'Email', 'Nama Kelas', 'Tahun Ajaran'];
    }
}
