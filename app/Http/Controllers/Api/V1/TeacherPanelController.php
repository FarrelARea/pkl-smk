<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DailyLog;
use App\Models\DailyLogComment;
use App\Models\Evaluation;
use App\Models\Internship;
use App\Models\PermissionRequest;
use App\Models\StudentDocument;
use App\Models\TeacherStudentAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherPanelController extends Controller
{
    public function students(Request $request): JsonResponse
    {
        $teacher = auth('api')->user();

        $studentQuery = $teacher->assignedStudents()->where('role', 'student');

        if ($request->has('academic_year')) {
            $academicYear = $request->academic_year;
            $studentQuery->whereHas('classes', function ($q) use ($academicYear) {
                $q->where('academic_year', $academicYear);
            });
        }

        $studentIds = $studentQuery->pluck('users.id');

        $pendingPermissions = PermissionRequest::whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->selectRaw('student_id, count(*) as cnt')
            ->groupBy('student_id')
            ->pluck('cnt', 'student_id');

        $pendingDocuments = StudentDocument::whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->selectRaw('student_id, count(*) as cnt')
            ->groupBy('student_id')
            ->pluck('cnt', 'student_id');

        $studentsQuery = $teacher->assignedStudents()
            ->where('role', 'student');

        if ($request->has('academic_year')) {
            $academicYear = $request->academic_year;
            $studentsQuery->whereHas('classes', function ($q) use ($academicYear) {
                $q->where('academic_year', $academicYear);
            });
        }

        $students = $studentsQuery
            ->with(['internships' => fn($q) => $q->where('status', 'active')->with('company')])
            ->get()
            ->map(function ($student) use ($pendingPermissions, $pendingDocuments) {
                $activeInternship = $student->internships->first();
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'pending_permission_count' => $pendingPermissions[$student->id] ?? 0,
                    'pending_document_count' => $pendingDocuments[$student->id] ?? 0,
                    'active_internship' => $activeInternship ? [
                        'id' => $activeInternship->id,
                        'company' => $activeInternship->company?->name,
                        'start_date' => $activeInternship->start_date,
                        'end_date' => $activeInternship->end_date,
                    ] : null,
                ];
            });

        return response()->json($students->values());
    }

    public function permissions(int $studentId): JsonResponse
    {
        $teacher = auth('api')->user();

        if (!$this->isAssigned($teacher->id, $studentId)) {
            return response()->json(['error' => 'Murid tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $permissions = PermissionRequest::where('student_id', $studentId)
            ->orderByDesc('request_date')
            ->get(['id', 'type', 'request_date', 'end_date', 'reason', 'status', 'handler_note']);

        return response()->json($permissions);
    }

    public function approvePermission(Request $request, int $id): JsonResponse
    {
        $teacher = auth('api')->user();
        $permission = PermissionRequest::findOrFail($id);

        if (!$this->isAssigned($teacher->id, $permission->student_id)) {
            return response()->json(['error' => 'Murid tidak ada dalam tanggung jawab Anda.'], 403);
        }

        if (!$permission->isPending()) {
            return response()->json(['error' => 'Pengajuan ini sudah diproses.'], 422);
        }

        $data = $request->validate(['note' => 'nullable|string|max:500']);
        $permission->approve($teacher->id, $data['note'] ?? null);

        return response()->json(['message' => 'Pengajuan disetujui.', 'permission' => $permission]);
    }

    public function rejectPermission(Request $request, int $id): JsonResponse
    {
        $teacher = auth('api')->user();
        $permission = PermissionRequest::findOrFail($id);

        if (!$this->isAssigned($teacher->id, $permission->student_id)) {
            return response()->json(['error' => 'Murid tidak ada dalam tanggung jawab Anda.'], 403);
        }

        if (!$permission->isPending()) {
            return response()->json(['error' => 'Pengajuan ini sudah diproses.'], 422);
        }

        $data = $request->validate(['note' => 'nullable|string|max:500']);
        $permission->reject($teacher->id, $data['note'] ?? null);

        return response()->json(['message' => 'Pengajuan ditolak.', 'permission' => $permission]);
    }

    public function logs(int $studentId): JsonResponse
    {
        $teacher = auth('api')->user();

        if (!$this->isAssigned($teacher->id, $studentId)) {
            return response()->json(['error' => 'Murid tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $logs = DailyLog::where('student_id', $studentId)
            ->with(['comments' => fn($q) => $q->with('author:id,name')])
            ->orderByDesc('log_date')
            ->get(['id', 'log_date', 'activities', 'reflection', 'review_status', 'review_note', 'created_at']);

        return response()->json($logs);
    }

    public function addComment(Request $request, int $logId): JsonResponse
    {
        $teacher = auth('api')->user();
        $log = DailyLog::findOrFail($logId);

        if (!$this->isAssigned($teacher->id, $log->student_id)) {
            return response()->json(['error' => 'Murid tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $data = $request->validate(['comment' => 'required|string']);

        $comment = DailyLogComment::create([
            'daily_log_id' => $log->id,
            'author_id' => $teacher->id,
            'author_role' => 'teacher',
            'comment' => $data['comment'],
        ]);

        $comment->load('author:id,name');

        return response()->json($comment, 201);
    }

    public function review(Request $request, int $logId): JsonResponse
    {
        $teacher = auth('api')->user();
        $log = DailyLog::findOrFail($logId);

        if (!$this->isAssigned($teacher->id, $log->student_id)) {
            return response()->json(['error' => 'Murid tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:needs_revision,approved',
            'note' => 'nullable|string|max:1000',
        ]);

        $log->update([
            'review_status' => $data['status'],
            'review_note' => $data['note'] ?? null,
        ]);

        return response()->json(['message' => 'Review berhasil disimpan.', 'log' => $log->fresh()]);
    }

    public function pending(): JsonResponse
    {
        $teacher = auth('api')->user();
        $studentIds = $teacher->assignedStudents()->where('role', 'student')->pluck('users.id');

        $permissions = PermissionRequest::whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->with('student:id,name')
            ->orderByDesc('request_date')
            ->get(['id', 'student_id', 'type', 'request_date', 'end_date', 'reason', 'status']);

        $documents = StudentDocument::whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->with('student:id,name')
            ->orderByDesc('created_at')
            ->get(['id', 'student_id', 'title', 'file_type', 'status', 'created_at']);

        return response()->json([
            'permissions' => $permissions,
            'documents' => $documents,
        ]);
    }

    public function studentStats(int $studentId): JsonResponse
    {
        $teacher = auth('api')->user();

        if (!$this->isAssigned($teacher->id, $studentId)) {
            return response()->json(['error' => 'Murid tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $student = User::findOrFail($studentId);
        $activeInternship = Internship::with('company')
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->first();

        $totalPresent = Attendance::where('student_id', $studentId)
            ->where('status', 'present')->count();

        $totalLogs = DailyLog::where('student_id', $studentId)->count();

        $permissionStats = PermissionRequest::where('student_id', $studentId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $evaluation = null;
        if ($activeInternship) {
            $evaluation = Evaluation::where('student_id', $studentId)
                ->where('internship_id', $activeInternship->id)
                ->where('evaluator_id', $teacher->id)
                ->first(['score', 'comments', 'updated_at']);
        }

        return response()->json([
            'student' => ['id' => $student->id, 'name' => $student->name, 'email' => $student->email],
            'student_name' => $student->name,
            'company_name' => $activeInternship?->company?->name,
            'active_internship_id' => $activeInternship?->id,
            'total_present' => $totalPresent,
            'total_logs' => $totalLogs,
            'permission_requests' => $permissionStats,
            'evaluation' => $evaluation,
        ]);
    }

    public function evaluate(Request $request, int $studentId): JsonResponse
    {
        $teacher = auth('api')->user();

        if (!$this->isAssigned($teacher->id, $studentId)) {
            return response()->json(['error' => 'Murid tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $data = $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'comments' => 'nullable|string',
        ]);

        $activeInternship = Internship::where('student_id', $studentId)
            ->where('status', 'active')
            ->first();

        if (!$activeInternship) {
            return response()->json(['error' => 'Murid tidak memiliki magang aktif.'], 422);
        }

        $evaluation = Evaluation::updateOrCreate(
            [
                'student_id' => $studentId,
                'internship_id' => $activeInternship->id,
                'evaluator_id' => $teacher->id,
            ],
            [
                'score' => $data['score'],
                'comments' => $data['comments'] ?? null,
                'type' => 'teacher',
            ]
        );

        return response()->json([
            'message' => 'Penilaian berhasil disimpan.',
            'evaluation' => $evaluation,
        ]);
    }

    private function isAssigned(int $teacherId, int $studentId): bool
    {
        return TeacherStudentAssignment::where('teacher_id', $teacherId)
            ->where('student_id', $studentId)
            ->exists();
    }
}
