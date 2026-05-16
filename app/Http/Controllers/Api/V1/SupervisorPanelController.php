<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\DailyLogComment;
use App\Models\Evaluation;
use App\Models\Internship;
use App\Models\PermissionRequest;
use App\Models\StudentDocument;
use App\Models\User;
use App\Services\AttendanceInferenceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupervisorPanelController extends Controller
{
    public function __construct(private AttendanceInferenceService $attendanceInferenceService)
    {
    }

    public function dashboardSummary(): JsonResponse
    {
        $supervisor = auth('api')->user();
        $students = $this->supervisedStudents($supervisor);
        $studentIds = $students->pluck('id');
        $today = Carbon::today()->toDateString();

        $pendingPermissions = PermissionRequest::whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->count();

        $pendingDocuments = StudentDocument::whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->count();

        $submittedEvaluations = Evaluation::whereIn('student_id', $studentIds)
            ->count();

        $studentNames = $students->pluck('name', 'id');
        $attendanceToday = $this->attendanceInferenceService
            ->attendanceForStudentsOnDate($studentIds, $today)
            ->map(fn ($record) => [
                'student_id' => $record['student_id'],
                'student_name' => $studentNames[$record['student_id']] ?? null,
                'attendance_date' => $record['attendance_date'],
                'status' => $record['status'],
                'notes' => $record['notes'],
            ])
            ->values();

        $activeInternshipCount = $students
            ->filter(fn ($student) => $student->internships->isNotEmpty())
            ->count();

        $studentItems = $students->map(function ($student) {
            $activeInternship = $student->internships->first();

            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'class_names' => $student->classes->pluck('name')->filter()->values(),
                'academic_years' => $student->classes->pluck('academic_year')->filter()->unique()->values(),
                'active_internship' => $activeInternship ? [
                    'id' => $activeInternship->id,
                    'company' => $activeInternship->company?->name,
                    'start_date' => $activeInternship->start_date,
                    'end_date' => $activeInternship->end_date,
                ] : null,
            ];
        })->values();

        return response()->json([
            'summary' => [
                'assigned_students' => $students->count(),
                'active_internships' => $activeInternshipCount,
                'pending_permissions' => $pendingPermissions,
                'pending_documents' => $pendingDocuments,
                'submitted_evaluations' => $submittedEvaluations,
                'attendance_today_count' => $attendanceToday->where('status', 'present')->count(),
            ],
            'today' => [
                'date' => $today,
                'attendance' => $attendanceToday,
            ],
            'company_scope' => [
                'company' => $supervisor->company?->only(['id', 'name']),
            ],
            'students' => $studentItems,
        ]);
    }

    public function attendanceCalendar(Request $request): JsonResponse
    {
        $supervisor = auth('api')->user();
        $students = $this->supervisedStudents($supervisor);
        $studentIds = $students->pluck('id');
        $date = Carbon::parse($request->query('date', Carbon::today()->toDateString()))->toDateString();
        $studentNames = $students->pluck('name', 'id');

        $records = $this->attendanceInferenceService
            ->attendanceForStudentsOnDate($studentIds, $date)
            ->map(fn ($record) => [
                'student_id' => $record['student_id'],
                'student_name' => $studentNames[$record['student_id']] ?? null,
                'attendance_date' => $record['attendance_date'],
                'status' => $record['status'],
                'notes' => $record['notes'],
            ])
            ->values();

        return response()->json([
            'date' => $date,
            'attendance' => $records,
            'present_count' => $records->where('status', 'present')->count(),
            'tracked_count' => $records->count(),
        ]);
    }

    public function documentsOverview(): JsonResponse
    {
        $supervisor = auth('api')->user();
        $studentIds = $this->supervisedStudents($supervisor)->pluck('id');

        $documents = StudentDocument::with('student:id,name')
            ->whereIn('student_id', $studentIds)
            ->orderByDesc('created_at')
            ->get(['id', 'student_id', 'title', 'file_type', 'file_path', 'status', 'teacher_note', 'created_at'])
            ->map(fn ($document) => [
                'id' => $document->id,
                'student_id' => $document->student_id,
                'student_name' => $document->student?->name,
                'title' => $document->title,
                'file_type' => $document->file_type,
                'file_path' => $document->file_path,
                'status' => $document->status,
                'teacher_note' => $document->teacher_note,
                'created_at' => $document->created_at,
            ])
            ->values();

        return response()->json($documents);
    }

    public function scoreOverview(): JsonResponse
    {
        $supervisor = auth('api')->user();
        $students = $this->supervisedStudents($supervisor)->get(['users.id', 'users.name', 'users.email']);

        $items = $students->map(function ($student) use ($supervisor) {
            $activeInternship = Internship::with('company:id,name')
                ->where('student_id', $student->id)
                ->where('company_id', $supervisor->company_id)
                ->where('status', 'active')
                ->first();

            $evaluation = null;
            if ($activeInternship) {
                $evaluation = Evaluation::where('student_id', $student->id)
                    ->where('internship_id', $activeInternship->id)
                    ->first(['id', 'score', 'comments', 'updated_at']);
            }

            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_email' => $student->email,
                'company_name' => $activeInternship?->company?->name,
                'evaluation' => $evaluation,
                'detail_url' => "/supervisor/students/{$student->id}",
            ];
        })->values();

        return response()->json($items);
    }

    public function permissionsOverview(): JsonResponse
    {
        $supervisor = auth('api')->user();
        $studentIds = $this->supervisedStudents($supervisor)->pluck('id');

        $permissions = PermissionRequest::with('student:id,name')
            ->whereIn('student_id', $studentIds)
            ->orderByDesc('request_date')
            ->get(['id', 'student_id', 'type', 'request_date', 'end_date', 'reason', 'status', 'handler_note'])
            ->map(fn ($permission) => [
                'id' => $permission->id,
                'student_id' => $permission->student_id,
                'student_name' => $permission->student?->name,
                'type' => $permission->type,
                'request_date' => $permission->request_date?->toDateString(),
                'end_date' => $permission->end_date?->toDateString(),
                'reason' => $permission->reason,
                'status' => $permission->status,
                'handler_note' => $permission->handler_note,
            ])
            ->values();

        return response()->json($permissions);
    }

    public function studentStats(int $studentId): JsonResponse
    {
        $supervisor = auth('api')->user();

        if (!$this->canAccessStudent($supervisor, $studentId)) {
            return response()->json(['error' => 'Siswa tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $student = User::findOrFail($studentId);
        $activeInternship = Internship::with('company')
            ->where('student_id', $studentId)
            ->where('company_id', $supervisor->company_id)
            ->where('status', 'active')
            ->first();

        $totalPresent = $this->attendanceInferenceService->totalPresentDays(
            $studentId,
            $activeInternship?->id,
        );

        $totalLogs = DailyLog::where('student_id', $studentId)->count();

        $permissionStats = PermissionRequest::where('student_id', $studentId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $evaluation = null;
        if ($activeInternship) {
            $evaluation = Evaluation::where('student_id', $studentId)
                ->where('internship_id', $activeInternship->id)
                ->first(['id', 'score', 'comments', 'updated_at']);
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
        $supervisor = auth('api')->user();

        if (!$this->canAccessStudent($supervisor, $studentId)) {
            return response()->json(['error' => 'Siswa tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $data = $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'comments' => 'nullable|string',
        ]);

        $activeInternship = Internship::where('student_id', $studentId)
            ->where('company_id', $supervisor->company_id)
            ->where('status', 'active')
            ->first();

        if (!$activeInternship) {
            return response()->json(['error' => 'Siswa tidak memiliki magang aktif pada perusahaan Anda.'], 422);
        }

        $evaluation = Evaluation::updateOrCreate(
            [
                'student_id' => $studentId,
                'internship_id' => $activeInternship->id,
            ],
            [
                'evaluator_id' => $supervisor->id,
                'last_updated_by_id' => $supervisor->id,
                'score' => $data['score'],
                'comments' => $data['comments'] ?? null,
                'type' => 'supervisor',
            ]
        );

        return response()->json([
            'message' => 'Penilaian pembimbing berhasil disimpan.',
            'evaluation' => $evaluation,
        ]);
    }

    public function permissions(int $studentId): JsonResponse
    {
        $supervisor = auth('api')->user();

        if (!$this->canAccessStudent($supervisor, $studentId)) {
            return response()->json(['error' => 'Siswa tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $permissions = PermissionRequest::where('student_id', $studentId)
            ->orderByDesc('request_date')
            ->get(['id', 'type', 'request_date', 'end_date', 'reason', 'status', 'handler_note']);

        return response()->json($permissions);
    }

    public function approvePermission(Request $request, int $id): JsonResponse
    {
        return $this->handlePermission($request, $id, 'approve');
    }

    public function rejectPermission(Request $request, int $id): JsonResponse
    {
        return $this->handlePermission($request, $id, 'reject');
    }

    public function logs(int $studentId): JsonResponse
    {
        $supervisor = auth('api')->user();

        if (!$this->canAccessStudent($supervisor, $studentId)) {
            return response()->json(['error' => 'Siswa tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $logs = DailyLog::where('student_id', $studentId)
            ->with(['comments' => fn ($query) => $query->with('author:id,name')])
            ->orderByDesc('log_date')
            ->get(['id', 'log_date', 'activities', 'reflection', 'review_status', 'review_note', 'created_at']);

        return response()->json($logs);
    }

    public function addComment(Request $request, int $logId): JsonResponse
    {
        $supervisor = auth('api')->user();
        $log = DailyLog::findOrFail($logId);

        if (!$this->canAccessStudent($supervisor, $log->student_id)) {
            return response()->json(['error' => 'Siswa tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $data = $request->validate(['comment' => 'required|string']);

        $comment = DailyLogComment::create([
            'daily_log_id' => $log->id,
            'author_id' => $supervisor->id,
            'author_role' => 'company_supervisor',
            'comment' => $data['comment'],
        ]);

        $comment->load('author:id,name');

        return response()->json($comment, 201);
    }

    public function review(Request $request, int $logId): JsonResponse
    {
        $supervisor = auth('api')->user();
        $log = DailyLog::findOrFail($logId);

        if (!$this->canAccessStudent($supervisor, $log->student_id)) {
            return response()->json(['error' => 'Siswa tidak ada dalam tanggung jawab Anda.'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:needs_revision,approved',
            'note' => 'nullable|string|max:1000',
        ]);

        $log->update([
            'review_status' => $data['status'],
            'review_note' => $data['note'] ?? null,
        ]);

        return response()->json([
            'message' => 'Review log berhasil disimpan.',
            'log' => $log->fresh(),
        ]);
    }

    private function handlePermission(Request $request, int $id, string $action): JsonResponse
    {
        $supervisor = auth('api')->user();
        $permission = PermissionRequest::findOrFail($id);

        if (!$this->canAccessStudent($supervisor, $permission->student_id)) {
            return response()->json(['error' => 'Siswa tidak ada dalam tanggung jawab Anda.'], 403);
        }

        if (!$permission->isPending()) {
            return response()->json(['error' => 'Pengajuan ini sudah diproses.'], 422);
        }

        $data = $request->validate(['note' => 'nullable|string|max:500']);

        if ($action === 'approve') {
            $permission->approve($supervisor->id, $data['note'] ?? null);
            $message = 'Pengajuan berhasil disetujui.';
        } else {
            $permission->reject($supervisor->id, $data['note'] ?? null);
            $message = 'Pengajuan berhasil ditolak.';
        }

        return response()->json([
            'message' => $message,
            'permission' => $permission,
        ]);
    }

    private function supervisedStudents(User $supervisor)
    {
        return $supervisor->getMyStudents()->load([
            'classes:id,name,academic_year',
            'internships' => fn ($query) => $query
                ->where('status', 'active')
                ->where('company_id', $supervisor->company_id)
                ->with('company:id,name'),
        ]);
    }

    private function canAccessStudent(User $supervisor, int $studentId): bool
    {
        return $supervisor->getMyStudents()->pluck('id')->contains($studentId);
    }
}
