<?php

namespace App\Imports;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SchoolAdminsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): Model|array|null
    {
        $school = School::firstOrCreate(
            ['name' => $row['nama_sekolah']]
        );

        return User::updateOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['nama'],
                'role' => 'school_admin',
                'school_id' => $school->id,
                'password' => isset($row['password']) ? bcrypt($row['password']) : bcrypt('password'),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nama_sekolah' => 'required|string',
            'nama' => 'required|string',
            'email' => 'required|email',
        ];
    }
}
