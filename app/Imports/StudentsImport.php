<?php

namespace App\Imports;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $school = School::where('name', $row['school_name'])->first();
        
        if (!$school) {
            $school = School::create(['name' => $row['school_name']]);
        }

        $student = User::updateOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['name'],
                'role' => 'student',
                'school_id' => $school->id,
                'password' => isset($row['password']) ? bcrypt($row['password']) : bcrypt('password'),
            ]
        );

        if (!empty($row['class_names'])) {
            $classNames = explode(',', $row['class_names']);
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