<?php

namespace App\Exports;

use App\Models\School;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SchoolsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return School::all()->map(function ($school) {
            return [
                'name' => $school->name,
                'address' => $school->address,
                'phone' => $school->phone,
                'email' => $school->email,
            ];
        });
    }

    public function headings(): array
    {
        return ['name', 'address', 'phone', 'email'];
    }
}