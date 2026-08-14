<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\StudentDocument;
use App\Models\TeacherStudentAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentApprovalController extends Controller
{
    public function teacherDocuments(int $studentId): JsonResponse
    {
        $teacher = auth('api')->user();

        if (!$this->canAccessTeacherStudent($teacher, $studentId)) {
            return response()->json(['error' => 'Unauthorized: student bukan bagian dari kelas Anda.'], 403);
        }

        $documents = StudentDocument::where('student_id', $studentId)
            ->with(['approvedBy'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($documents);
    }

    public function supervisorDocuments(int $studentId): JsonResponse
    {
        $supervisor = auth('api')->user();

        if (!$this->canAccessSupervisorStudent($supervisor, $studentId)) {
            return response()->json(['error' => 'Unauthorized: student bukan bagian dari perusahaan Anda.'], 403);
        }

        $documents = StudentDocument::where('student_id', $studentId)
            ->with(['approvedBy'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($documents);
    }

    public function approve(int $id): JsonResponse
    {
        $user = auth('api')->user();
        $document = StudentDocument::findOrFail($id);

        if (!$this->canAccessTeacherStudent($user, $document->student_id)) {
            return response()->json(['error' => 'Unauthorized: student bukan bagian dari kelas Anda.'], 403);
        }

        if ($document->status !== 'pending') {
            return response()->json(['error' => 'Hanya dokumen berstatus pending yang dapat di-approve.'], 422);
        }

        $document->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Dokumen berhasil di-approve.',
            'document' => $document->fresh(['approvedBy']),
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $user = auth('api')->user();
        $document = StudentDocument::findOrFail($id);

        if (!$this->canAccessTeacherStudent($user, $document->student_id)) {
            return response()->json(['error' => 'Unauthorized: student bukan bagian dari kelas Anda.'], 403);
        }

        $data = $request->validate([
            'teacher_note' => 'required|string',
        ]);

        if ($document->status !== 'pending') {
            return response()->json(['error' => 'Hanya dokumen berstatus pending yang dapat di-reject.'], 422);
        }

        $document->update([
            'status' => 'rejected',
            'teacher_note' => $data['teacher_note'],
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Dokumen berhasil di-reject.',
            'document' => $document->fresh(['approvedBy']),
        ]);
    }

    public function cancelApproval(int $id): JsonResponse
    {
        $user = auth('api')->user();
        $document = StudentDocument::findOrFail($id);

        if (!$this->canAccessTeacherStudent($user, $document->student_id)) {
            return response()->json(['error' => 'Unauthorized: student bukan bagian dari kelas Anda.'], 403);
        }

        if (!in_array($document->status, ['approved', 'rejected'], true)) {
            return response()->json(['error' => 'Hanya dokumen yang sudah diproses yang dapat dibatalkan.'], 422);
        }

        $document->update([
            'status' => 'pending',
            'teacher_note' => null,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return response()->json([
            'message' => 'Persetujuan dokumen berhasil dibatalkan.',
            'document' => $document->fresh(['approvedBy']),
        ]);
    }

    public function supervisorApprove(int $id): JsonResponse
    {
        $user = auth('api')->user();
        $document = StudentDocument::findOrFail($id);

        if (!$user->isCompanySupervisor()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$this->canAccessSupervisorStudent($user, $document->student_id)) {
            return response()->json(['error' => 'Unauthorized: student bukan bagian dari perusahaan Anda.'], 403);
        }

        if ($document->status !== 'pending') {
            return response()->json(['error' => 'Hanya dokumen berstatus pending yang dapat di-approve.'], 422);
        }

        $document->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Dokumen berhasil di-approve oleh supervisor.',
            'document' => $document->fresh(['approvedBy']),
        ]);
    }

    public function supervisorReject(Request $request, int $id): JsonResponse
    {
        $user = auth('api')->user();
        $document = StudentDocument::findOrFail($id);

        if (!$user->isCompanySupervisor()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$this->canAccessSupervisorStudent($user, $document->student_id)) {
            return response()->json(['error' => 'Unauthorized: student bukan bagian dari perusahaan Anda.'], 403);
        }

        $data = $request->validate([
            'teacher_note' => 'required|string',
        ]);

        if ($document->status !== 'pending') {
            return response()->json(['error' => 'Hanya dokumen berstatus pending yang dapat di-reject.'], 422);
        }

        $document->update([
            'status' => 'rejected',
            'teacher_note' => $data['teacher_note'],
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Dokumen berhasil di-reject oleh supervisor.',
            'document' => $document->fresh(['approvedBy']),
        ]);
    }

    public function supervisorCancelApproval(int $id): JsonResponse
    {
        $user = auth('api')->user();
        $document = StudentDocument::findOrFail($id);

        if (!$user->isCompanySupervisor()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$this->canAccessSupervisorStudent($user, $document->student_id)) {
            return response()->json(['error' => 'Unauthorized: student bukan bagian dari perusahaan Anda.'], 403);
        }

        if (!in_array($document->status, ['approved', 'rejected'], true)) {
            return response()->json(['error' => 'Hanya dokumen yang sudah diproses yang dapat dibatalkan.'], 422);
        }

        $document->update([
            'status' => 'pending',
            'teacher_note' => null,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return response()->json([
            'message' => 'Persetujuan dokumen supervisor berhasil dibatalkan.',
            'document' => $document->fresh(['approvedBy']),
        ]);
    }

    private function canAccessTeacherStudent(User $teacher, int $studentId): bool
    {
        $student = User::findOrFail($studentId);
        $teacherClassIds = $teacher->teacherClasses()->pluck('classes.id');
        $studentClassIds = $student->studentClasses()->pluck('classes.id');
        $sharedClasses = $teacherClassIds->intersect($studentClassIds);
        $isAssigned = TeacherStudentAssignment::where('teacher_id', $teacher->id)
            ->where('student_id', $studentId)
            ->exists();

        return $sharedClasses->isNotEmpty() || $isAssigned;
    }

    private function canAccessSupervisorStudent(User $supervisor, int $studentId): bool
    {
        return Internship::where('student_id', $studentId)
            ->where('supervisor_id', $supervisor->id)
            ->where('status', 'active')
            ->exists();
    }
}
