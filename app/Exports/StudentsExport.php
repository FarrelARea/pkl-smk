<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::where('role', 'student')->with(['school', 'classes'])->get()->map(function ($student) {
            $classNames = $student->classes->pluck('name')->join(', ');
            return [
                'name' => $student->name,
                'email' => $student->email,
                'school_name' => $student->school->name ?? '',
                'class_names' => $classNames,
            ];
        });
    }

    public function headings(): array
    {
        return ['name', 'email', 'school_name', 'class_names'];
    }
}