<?php

namespace App\Imports;

use App\Models\User;
use App\Models\TeacherStudentAssignment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeacherStudentAssignmentsImport implements ToModel, WithHeadingRow, WithValidation
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

        $student = User::where('email', $row['email_siswa'])
            ->where('role', 'student')
            ->first();

        if (!$student) {
            $student = User::firstOrCreate(
                ['email' => $row['email_siswa']],
                [
                    'name' => explode('@', $row['email_siswa'])[0],
                    'role' => 'student',
                    'password' => bcrypt('password'),
                ]
            );
        }

        return TeacherStudentAssignment::firstOrCreate([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'email_guru' => 'required|email',
            'email_siswa' => 'required|email',
        ];
    }
}
