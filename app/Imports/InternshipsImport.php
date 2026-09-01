<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Internship;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class InternshipsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): Model|array|null
    {
        $student = User::firstOrCreate(
            ['email' => $row['email_siswa']],
            [
                'name' => $row['nama_siswa'] ?? explode('@', $row['email_siswa'])[0],
                'role' => 'student',
                'password' => bcrypt('password'),
            ]
        );

        $school = School::firstOrCreate(
            ['name' => $row['nama_sekolah']]
        );

        $company = Company::firstOrCreate(
            [
                'name' => $row['nama_perusahaan'],
                'school_id' => $school->id,
            ]
        );

        $supervisorId = null;
        if (!empty($row['email_pembimbing'])) {
            $supervisor = User::firstOrCreate(
                ['email' => $row['email_pembimbing']],
                [
                    'name' => $row['nama_pembimbing'] ?? explode('@', $row['email_pembimbing'])[0],
                    'role' => 'company_supervisor',
                    'school_id' => $school->id,
                    'company_id' => $company->id,
                    'password' => bcrypt('password'),
                ]
            );
            $supervisorId = $supervisor->id;
        }

        return Internship::updateOrCreate(
            [
                'student_id' => $student->id,
                'company_id' => $company->id,
            ],
            [
                'supervisor_id' => $supervisorId,
                'start_date' => $row['tanggal_mulai'],
                'end_date' => $row['tanggal_selesai'],
                'status' => $row['status'] ?? 'active',
                'notes' => $row['catatan'] ?? null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'email_siswa' => 'required|email',
            'nama_sekolah' => 'required|string',
            'nama_perusahaan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'status' => 'nullable|in:active,completed,cancelled',
        ];
    }
}
