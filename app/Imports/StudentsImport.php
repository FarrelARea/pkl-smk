<?php

namespace App\Imports;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Model|array|null
    {
        $school = School::firstOrCreate(
            ['name' => $row['nama_sekolah']]
        );

        $student = User::updateOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['nama'],
                'role' => 'student',
                'school_id' => $school->id,
                'password' => isset($row['password']) ? bcrypt($row['password']) : bcrypt('password'),
            ]
        );

        if (!empty($row['nama_kelas'])) {
            $classNames = explode(',', $row['nama_kelas']);
            foreach ($classNames as $className) {
                $className = trim($className);
                $class = SchoolClass::firstOrCreate(
                    ['name' => $className, 'school_id' => $school->id]
                );
                $student->classes()->syncWithoutDetaching([
                    $class->id => ['role' => 'student']
                ]);
            }
        }

        return $student;
    }
}
