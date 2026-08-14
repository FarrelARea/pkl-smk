<?php

namespace App\Services;

use App\Models\DocumentRequirement;
use App\Models\User;

class DocumentRequirementService
{
    /**
     * Find the most specific document requirement for a student.
     * Priority: (class_id + teacher_id) > (class_id only) > (teacher_id only) > null
     */
    public function findForStudent(User $student): ?DocumentRequirement
    {
        $classIds = $student->studentClasses()->pluck('classes.id');

        // 1. class + teacher combination
        $req = DocumentRequirement::whereIn('class_id', $classIds)
            ->whereNotNull('teacher_id')
            ->first();

        if ($req) {
            return $req;
        }

        // 2. class only
        $req = DocumentRequirement::whereIn('class_id', $classIds)
            ->whereNull('teacher_id')
            ->first();

        if ($req) {
            return $req;
        }

        // 3. teacher only — find teacher IDs linked to student's classes
        $teacherIds = \DB::table('class_user')
            ->whereIn('class_id', $classIds)
            ->where('role', 'teacher')
            ->pluck('user_id');

        $req = DocumentRequirement::whereNull('class_id')
            ->whereIn('teacher_id', $teacherIds)
            ->first();

        return $req;
    }
}
