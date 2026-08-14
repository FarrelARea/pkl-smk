<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AttendancePoint;
use App\Models\Internship;
use App\Models\TeacherCompanyAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendancePointController extends Controller
{
    private function authorizeCompanyAccess(int $companyId, string $context = 'read'): bool
    {
        $user = auth('api')->user();

        if ($user->isSchoolAdmin()) {
            return \App\Models\Company::whereKey($companyId)
                ->where('school_id', $user->school_id)
                ->exists();
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return TeacherCompanyAssignment::where('teacher_id', $user->id)
                ->where('company_id', $companyId)
                ->exists();
        }

        if ($user->isCompanySupervisor()) {
            return (int) $user->company_id === $companyId;
        }

        if ($user->isStudent() && $context === 'read') {
            return Internship::where('student_id', $user->id)
                ->where('company_id', $companyId)
                ->exists();
        }

        return false;
    }

    public function index(int $companyId): JsonResponse
    {
        if (!$this->authorizeCompanyAccess($companyId, 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = auth('api')->user();
        $query = AttendancePoint::with(['createdBy', 'approvedBy'])
            ->forCompany($companyId);

        // Students only see approved points
        if ($user->isStudent()) {
            $query->approved();
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request, int $companyId): JsonResponse
    {
        if (!$this->authorizeCompanyAccess($companyId, 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = auth('api')->user();
        if ($user->isStudent()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'distance_threshold' => 'sometimes|integer|min:1|max:10000',
        ]);

        $data['company_id'] = $companyId;
        $data['created_by'] = $user->id;
        $data['status'] = 'pending';

        $point = AttendancePoint::create($data);

        return response()->json($point->load(['company', 'createdBy']), 201);
    }

    public function show(int $companyId, int $id): JsonResponse
    {
        if (!$this->authorizeCompanyAccess($companyId, 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = auth('api')->user();
        $query = AttendancePoint::with(['company', 'createdBy', 'approvedBy'])
            ->forCompany($companyId)
            ->where('id', $id);

        if ($user->isStudent()) {
            $query->approved();
        }

        $point = $query->firstOrFail();

        return response()->json($point);
    }

    public function update(Request $request, int $companyId, int $id): JsonResponse
    {
        if (!$this->authorizeCompanyAccess($companyId, 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $point = AttendancePoint::forCompany($companyId)->findOrFail($id);
        $user = auth('api')->user();

        // Only admin can edit approved/rejected points; others can only edit pending ones they created
        if (!$user->isSchoolAdmin() && !$user->isSuperAdmin()) {
            if ($point->status !== 'pending' || $point->created_by !== $user->id) {
                return response()->json(['error' => 'Hanya bisa mengedit titik yang masih pending dan dibuat oleh Anda.'], 403);
            }
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'distance_threshold' => 'sometimes|integer|min:1|max:10000',
        ]);

        $point->update($data);

        return response()->json($point->load(['company', 'createdBy', 'approvedBy']));
    }

    public function destroy(int $companyId, int $id): JsonResponse
    {
        if (!$this->authorizeCompanyAccess($companyId, 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $point = AttendancePoint::forCompany($companyId)->findOrFail($id);
        $user = auth('api')->user();

        if (!$user->isSchoolAdmin() && !$user->isSuperAdmin()) {
            if ($point->status !== 'pending' || $point->created_by !== $user->id) {
                return response()->json(['error' => 'Hanya bisa menghapus titik yang masih pending dan dibuat oleh Anda.'], 403);
            }
        }

        $point->delete();

        return response()->json(['message' => 'Titik absensi berhasil dihapus.']);
    }

    public function approve(int $companyId, int $id): JsonResponse
    {
        $point = AttendancePoint::forCompany($companyId)->findOrFail($id);

        $point->update([
            'status' => 'approved',
            'approved_by' => auth('api')->id(),
            'approved_at' => now(),
        ]);

        return response()->json($point->load(['company', 'createdBy', 'approvedBy']));
    }

    public function reject(int $companyId, int $id): JsonResponse
    {
        $point = AttendancePoint::forCompany($companyId)->findOrFail($id);

        $point->update([
            'status' => 'rejected',
            'approved_by' => auth('api')->id(),
            'approved_at' => now(),
        ]);

        return response()->json($point->load(['company', 'createdBy', 'approvedBy']));
    }
}
