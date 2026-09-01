<?php

namespace App\Imports;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Model|array|null
    {
        $school = School::firstOrCreate(
            ['name' => $row['nama_sekolah']]
        );

        $teacher = User::updateOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['nama'],
                'role' => 'teacher',
                'school_id' => $school->id,
                'password' => isset($row['password']) ? bcrypt($row['password']) : bcrypt('password'),
            ]
        );

        return $teacher;
    }
}
