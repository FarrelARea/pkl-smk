<?php

namespace App\Exports;

use App\Models\School;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SchoolsExport implements FromCollection, WithHeadings
{
    public function collection(): Enumerable
    {
        return School::all()->map(function ($school) {
            return [
                'Nama' => $school->name,
                'Alamat' => $school->address,
                'Telepon' => $school->phone,
                'Email' => $school->email,
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama', 'Alamat', 'Telepon', 'Email'];
    }
}
