<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeachersExport implements FromCollection, WithHeadings
{
    public function collection(): Enumerable
    {
        return User::where('role', 'teacher')->with(['school', 'teacherClasses'])->get()->map(function ($teacher) {
            $classNames = $teacher->teacherClasses->pluck('name')->join(', ');
            return [
                'Nama Sekolah' => $teacher->school->name ?? '',
                'Nama' => $teacher->name,
                'Email' => $teacher->email,
                'Kelas' => $classNames,
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama Sekolah', 'Nama', 'Email', 'Kelas'];
    }
}
