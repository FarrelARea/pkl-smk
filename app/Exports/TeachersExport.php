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
                'name' => $teacher->name,
                'email' => $teacher->email,
                'school_name' => $teacher->school->name ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['name', 'email', 'school_name'];
    }
}