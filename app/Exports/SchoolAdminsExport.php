<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SchoolAdminsExport implements FromCollection, WithHeadings
{
    public function collection(): Enumerable
    {
        return User::where('role', 'school_admin')
            ->with('school')
            ->get()
            ->map(function ($admin) {
                return [
                    'Nama Sekolah' => $admin->school->name ?? '',
                    'Nama' => $admin->name,
                    'Email' => $admin->email,
                ];
            });
    }

    public function headings(): array
    {
        return ['Nama Sekolah', 'Nama', 'Email'];
    }
}
