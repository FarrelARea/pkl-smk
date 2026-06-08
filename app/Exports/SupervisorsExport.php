<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SupervisorsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::where('role', 'company_supervisor')
            ->with(['school', 'company'])
            ->get()
            ->map(function ($supervisor) {
                return [
                    'Nama Sekolah' => $supervisor->school->name ?? '',
                    'Nama Perusahaan' => $supervisor->company->name ?? '',
                    'Nama' => $supervisor->name,
                    'Email' => $supervisor->email,
                ];
            });
    }

    public function headings(): array
    {
        return ['Nama Sekolah', 'Nama Perusahaan', 'Nama', 'Email'];
    }
}
