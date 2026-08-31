<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\DailyLogComment;
use App\Models\Evaluation;
use App\Models\Internship;
use App\Models\PermissionRequest;
use App\Models\StudentAssessment;
use App\Models\StudentDocument;
use App\Models\TeacherStudentAssignment;
use App\Models\User;
use App\Models\ClockInOut;
use App\Services\AttendanceInferenceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherPanelController extends Controller
{
    public function __construct(private AttendanceInferenceService $attendanceInferenceService)
    {
    }

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

    public function dashboardSummary(): JsonResponse
    {
        $teacher = auth('api')->user();
        $students = $teacher->assignedStudents()
            ->where('role', 'student')
            ->with(['classes:id,name,academic_year', 'internships' => fn($q) => $q->where('status', 'active')->with('company:id,name')])
            ->get();
        $studentIds = $students->pluck('id');
        $today = Carbon::today()->toDateString();

        $pendingPermissions = PermissionRequest::whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->count();

        $pendingDocuments = StudentDocument::whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->count();

        $submittedAssessments = StudentAssessment::whereIn('student_id', $studentIds)
            ->where('teacher_id', $teacher->id)
            ->where('status', 'submitted')
            ->count();

        $studentNames = $students->pluck('name', 'id');
        $attendanceToday = $this->attendanceInferenceService
            ->attendanceForStudentsOnDate($studentIds, $today)
            ->map(fn($record) => [
                'student_id' => $record['student_id'],
                'student_name' => $studentNames[$record['student_id']] ?? null,
                'attendance_date' => $record['attendance_date'],
                'status' => $record['status'],
                'notes' => $record['notes'],
            ])
            ->values();

        $activeInternshipCount = $students->filter(fn($student) => $student->internships->isNotEmpty())->count();
        $classNames = $students
            ->flatMap(fn($student) => $student->classes->map(fn($class) => [
                'id' => $class->id,
                'name' => $class->name,
                'academic_year' => $class->academic_year,
            ]))
            ->unique('id')
            ->values();

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
                'submitted_assessments' => $submittedAssessments,
                'attendance_today_count' => $attendanceToday->where('status', 'present')->count(),
            ],
            'today' => [
                'date' => $today,
                'attendance' => $attendanceToday,
            ],
            'school_scope' => [
                'classes' => $classNames,
            ],
            'students' => $studentItems,
        ]);
    }

    public function attendanceCalendar(Request $request): JsonResponse
    {
        $teacher = auth('api')->user();
        $studentIds = $teacher->assignedStudents()->where('role', 'student')->pluck('users.id');
        $date = $request->query('date', Carbon::today()->toDateString());
        $parsedDate = Carbon::parse($date)->toDateString();

        $studentNames = $teacher->assignedStudents()
            ->where('role', 'student')
            ->pluck('name', 'users.id');

        $records = $this->attendanceInferenceService
            ->attendanceForStudentsOnDate($studentIds, $parsedDate)
            ->map(fn($record) => [
                'student_id' => $record['student_id'],
                'student_name' => $studentNames[$record['student_id']] ?? null,
                'attendance_date' => $record['attendance_date'],
                'status' => $record['status'],
                'notes' => $record['notes'],
            ])
            ->values();

        // Fetch actual clock-in/out timestamps for students who have any records today
        $clockRecords = ClockInOut::whereIn('user_id', $studentIds)
            ->whereDate('created_at', $parsedDate)
            ->orderBy('created_at')
            ->get();
        $clockByStudent = $clockRecords->groupBy('user_id');

        $records = $records->map(function ($record) use ($clockByStudent) {
            if (!$clockByStudent->has($record['student_id'])) {
                return $record;
            }
            $clocks = $clockByStudent[$record['student_id']];
            $clockIn = $clocks->firstWhere('type', 'clock_in');
            $clockOut = $clocks->filter(fn($c) => $c->type === 'clock_out')->first();

            $record['clock_in'] = $clockIn ? $clockIn->created_at : null;
            $record['clock_out'] = $clockOut ? $clockOut->created_at : null;
            $record['all_clocks'] = $clocks->map(fn($c) => [
                'type' => $c->type,
                'time' => $c->created_at,
                'within_range' => (bool)$c->is_within_range,
            ])->values();

            return $record;
        });

        return response()->json([
            'date' => $parsedDate,
            'attendance' => $records,
            'present_count' => $records->where('status', 'present')->count(),
            'tracked_count' => $records->count(),
        ]);
    }

    public function documentsOverview(): JsonResponse
    {
        $teacher = auth('api')->user();
        $studentIds = $teacher->assignedStudents()->where('role', 'student')->pluck('users.id');

        $documents = StudentDocument::with('student:id,name')
            ->whereIn('student_id', $studentIds)
            ->orderByDesc('created_at')
            ->get(['id', 'student_id', 'title', 'file_type', 'file_path', 'status', 'teacher_note', 'created_at'])
            ->map(fn($document) => [
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
        $teacher = auth('api')->user();
        $students = $teacher->assignedStudents()->where('role', 'student')->get(['users.id', 'users.name', 'users.email']);

        $items = $students->map(function ($student) use ($teacher) {
            $activeInternship = Internship::with('company:id,name')
                ->where('student_id', $student->id)
                ->where('status', 'active')
                ->first();

            $assessment = null;
            if ($activeInternship) {
                $assessment = StudentAssessment::where('student_id', $student->id)
                    ->where('internship_id', $activeInternship->id)
                    ->where('teacher_id', $teacher->id)
                    ->first(['id', 'status', 'updated_at']);
            }

            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_email' => $student->email,
                'company_name' => $activeInternship?->company?->name,
                'assessment' => $assessment,
                'detail_url' => "/teacher/students/{$student->id}",
            ];
        })->values();

        return response()->json($items);
    }

    public function permissionsOverview(): JsonResponse
    {
        $teacher = auth('api')->user();
        $studentIds = $teacher->assignedStudents()->where('role', 'student')->pluck('users.id');

        $permissions = PermissionRequest::with('student:id,name')
            ->whereIn('student_id', $studentIds)
            ->orderByDesc('request_date')
            ->get(['id', 'student_id', 'type', 'request_date', 'end_date', 'reason', 'status', 'handler_note'])
            ->map(fn($permission) => [
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

    public function studentStats(Request $request, int $studentId): JsonResponse
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
                ->first(['score', 'comments', 'updated_at']);
        }

        // Clock-in/out history with date range filter, default current month
        $rangeStart = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $rangeEnd = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $clockHistory = ClockInOut::where('user_id', $studentId)
            ->whereDate('created_at', '>=', $rangeStart)
            ->whereDate('created_at', '<=', $rangeEnd)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn ($c) => $c->created_at->toDateString())
            ->map(function ($clocks, $day) {
                $clockIn = $clocks->firstWhere('type', 'clock_in');
                $clockOut = $clocks->filter(fn($c) => $c->type === 'clock_out')->first();
                return [
                    'date' => $day,
                    'clock_in' => $clockIn ? $clockIn->created_at : null,
                    'clock_out' => $clockOut ? $clockOut->created_at : null,
                    'all_clocks' => $clocks->sort()->map(fn($c) => [
                        'time' => $c->created_at,
                        'within_range' => (bool)$c->is_within_range,
                    ])->values(),
                ];
            })
            ->sortByDesc('date')
            ->values();

        return response()->json([
            'student' => ['id' => $student->id, 'name' => $student->name, 'email' => $student->email],
            'student_name' => $student->name,
            'company_name' => $activeInternship?->company?->name,
            'active_internship_id' => $activeInternship?->id,
            'total_present' => $totalPresent,
            'total_logs' => $totalLogs,
            'permission_requests' => $permissionStats,
            'evaluation' => $evaluation,
            'clock_history' => $clockHistory,
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
            ],
            [
                'evaluator_id' => $teacher->id,
                'last_updated_by_id' => $teacher->id,
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
