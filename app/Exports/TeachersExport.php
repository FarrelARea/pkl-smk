<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeachersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::where('role', 'teacher')->with('school')->get()->map(function ($teacher) {
            return [
                'Nama Sekolah' => $teacher->school->name ?? '',
                'Nama' => $teacher->name,
                'Email' => $teacher->email,
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama Sekolah', 'Nama', 'Email'];
    }
}
