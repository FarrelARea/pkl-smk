<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\TeacherCompanyAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherCompanyAssignmentController extends Controller
{
    private function resolveAccessibleTeacher(Request $request, int $teacherId): User
    {
        $teacher = User::findOrFail($teacherId);

        if (!$teacher->isTeacher()) {
            return response()->json(['error' => 'User yang dipilih bukan guru.'], 422);
        }

        if (!$teacher->belongsToAdminSchool($request->user())) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        return $teacher;
    }

    private function resolveAccessibleCompany(Request $request, int $companyId): Company
    {
        $company = Company::findOrFail($companyId);

        if (!$company->belongsToAdminSchool($request->user())) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        return $company;
    }

    public function index(Request $request): JsonResponse
    {
        $query = TeacherCompanyAssignment::with(['teacher', 'company']);

        if (!$request->user()->isSuperAdmin()) {
            $query->where('school_id', $request->user()->school_id);
        }

        if ($request->has('teacher_id')) {
            $teacher = $this->resolveAccessibleTeacher($request, (int) $request->teacher_id);
            $query->where('teacher_id', $teacher->id);
        }

        if ($request->has('company_id')) {
            $company = $this->resolveAccessibleCompany($request, (int) $request->company_id);
            $query->where('company_id', $company->id);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        $teacher = $this->resolveAccessibleTeacher($request, $data['teacher_id']);
        $company = $this->resolveAccessibleCompany($request, $data['company_id']);

        if ($teacher->school_id !== $company->school_id) {
            return response()->json(['error' => 'Guru dan perusahaan harus berasal dari sekolah yang sama.'], 422);
        }

        $assignment = TeacherCompanyAssignment::firstOrCreate(
            [
                'teacher_id' => $teacher->id,
                'company_id' => $company->id,
            ],
            [
                'school_id' => $company->school_id,
            ]
        );

        if (!$assignment->school_id) {
            $assignment->update(['school_id' => $company->school_id]);
        }

        return response()->json($assignment->load(['teacher', 'company']), 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $assignment = TeacherCompanyAssignment::findOrFail($id);

        if (!$request->user()->isSuperAdmin() && $assignment->school_id !== $request->user()->school_id) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $assignment->delete();

        return response()->json(['message' => 'Assignment berhasil dihapus.']);
    }

    public function myCompanies(): JsonResponse
    {
        $companies = auth('api')->user()->assignedCompanies()->get();

        return response()->json($companies);
    }
}
