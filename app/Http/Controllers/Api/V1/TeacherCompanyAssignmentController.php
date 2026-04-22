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
    public function index(Request $request): JsonResponse
    {
        $query = TeacherCompanyAssignment::with(['teacher', 'company']);

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        $teacher = User::findOrFail($data['teacher_id']);

        if (!$teacher->isTeacher()) {
            return response()->json(['error' => 'User yang dipilih bukan guru.'], 422);
        }

        $assignment = TeacherCompanyAssignment::firstOrCreate([
            'teacher_id' => $data['teacher_id'],
            'company_id' => $data['company_id'],
        ]);

        return response()->json($assignment->load(['teacher', 'company']), 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $assignment = TeacherCompanyAssignment::findOrFail($id);
        $assignment->delete();

        return response()->json(['message' => 'Assignment berhasil dihapus.']);
    }

    public function myCompanies(): JsonResponse
    {
        $companies = auth('api')->user()->assignedCompanies()->get();

        return response()->json($companies);
    }
}
