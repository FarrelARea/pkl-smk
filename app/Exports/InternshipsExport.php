<?php

namespace App\Exports;

use App\Models\Internship;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InternshipsExport implements FromCollection, WithHeadings
{
    public function collection(): Enumerable
    {
        return Internship::with(['student', 'company', 'company.school', 'supervisor'])->get()->map(function ($internship) {
            return [
                'Email Siswa' => $internship->student->email ?? '',
                'Nama Sekolah' => $internship->company->school->name ?? '',
                'Nama Perusahaan' => $internship->company->name ?? '',
                'Email Pembimbing' => $internship->supervisor->email ?? '',
                'Tanggal Mulai' => $internship->start_date,
                'Tanggal Selesai' => $internship->end_date,
                'Status' => $internship->status,
                'Catatan' => $internship->notes,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Email Siswa',
            'Nama Sekolah',
            'Nama Perusahaan',
            'Email Pembimbing',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Status',
            'Catatan',
        ];
    }
}
