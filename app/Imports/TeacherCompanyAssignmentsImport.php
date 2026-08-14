<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\User;
use App\Models\TeacherCompanyAssignment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeacherCompanyAssignmentsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $teacher = User::where('email', $row['email_guru'])
            ->where('role', 'teacher')
            ->first();

        if (!$teacher) {
            $teacher = User::firstOrCreate(
                ['email' => $row['email_guru']],
                [
                    'name' => explode('@', $row['email_guru'])[0],
                    'role' => 'teacher',
                    'password' => bcrypt('password'),
                ]
            );
        }

        $company = Company::where('name', $row['nama_perusahaan'])->first();

        if (!$company) {
            $company = Company::create([
                'name' => $row['nama_perusahaan'],
            ]);
        }

        return TeacherCompanyAssignment::firstOrCreate([
            'teacher_id' => $teacher->id,
            'company_id' => $company->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'email_guru' => 'required|email',
            'nama_perusahaan' => 'required|string',
        ];
    }
}
