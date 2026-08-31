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

        // Scope lookup to teacher's school first; only fall back to global if no school assigned yet
        // Guard: teacher must belong to a school; skip rows without one rather than creating orphans
        if (!$teacher->school_id) {
            return null;
        }

        $scopeLookup = Company::where('school_id', $teacher->school_id)->where('name', $row['nama_perusahaan']);
        $company = $scopeLookup->first();

        if (!$company) {
            $company = Company::create([
                'name'     => $row['nama_perusahaan'],
                'school_id'=> $teacher->school_id,
            ]);
        } else {
            // Populate school_id on orphaned company records too
            if (!$company->school_id) {
                $company->update(['school_id' => $teacher->school_id]);
            }
        }

        return TeacherCompanyAssignment::firstOrCreate([
            'teacher_id' => $teacher->id,
            'company_id' => $company->id,
        ], [
            'school_id' => $teacher->school_id,
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
