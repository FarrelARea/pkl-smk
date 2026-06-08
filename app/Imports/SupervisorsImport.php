<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\School;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SupervisorsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $school = School::firstOrCreate(
            ['name' => $row['nama_sekolah']]
        );

        $company = Company::firstOrCreate(
            [
                'name' => $row['nama_perusahaan'],
                'school_id' => $school->id,
            ]
        );

        return User::updateOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['nama'],
                'role' => 'company_supervisor',
                'school_id' => $school->id,
                'company_id' => $company->id,
                'password' => isset($row['password']) ? bcrypt($row['password']) : bcrypt('password'),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nama_sekolah' => 'required|string',
            'nama_perusahaan' => 'required|string',
            'nama' => 'required|string',
            'email' => 'required|email',
        ];
    }
}
