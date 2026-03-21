<?php

namespace App\Imports;

use App\Models\School;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $school = School::where('name', $row['school_name'])->first();
        
        if (!$school) {
            $school = School::create(['name' => $row['school_name']]);
        }

        $teacher = User::updateOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['name'],
                'role' => 'teacher',
                'school_id' => $school->id,
                'password' => isset($row['password']) ? bcrypt($row['password']) : bcrypt('password'),
            ]
        );

        return $teacher;
    }
}