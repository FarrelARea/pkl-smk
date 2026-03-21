<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClockInOut;
use App\Models\DailyLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentActivityController extends Controller
{
    public function myStudents(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user->isTeacher() && !$user->isCompanySupervisor()) {
            return response()->json(['error' => 'Unauthorized - only teachers and supervisors can view this'], 403);
        }

        $students = $user->getMyStudents()->load('classes', 'school');

        return response()->json([
            'students' => $students,
            'total' => $students->count(),
        ]);
    }

    public function studentLogs(Request $request, int $studentId): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->canViewStudent($user, $studentId)) {
            return response()->json(['error' => 'Unauthorized - student not under your supervision'], 403);
        }

        $query = DailyLog::with(['internship', 'teacher'])->where('student_id', $studentId);

        if ($request->has('start_date')) {
            $query->where('log_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('log_date', '<=', $request->end_date);
        }

        $logs = $query->orderBy('log_date', 'desc')->paginate($request->get('per_page', 15));

        return response()->json($logs);
    }

    public function studentAttendance(Request $request, int $studentId): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->canViewStudent($user, $studentId)) {
            return response()->json(['error' => 'Unauthorized - student not under your supervision'], 403);
        }

        $query = Attendance::with(['internship', 'recordedBy'])->where('student_id', $studentId);

        if ($request->has('start_date')) {
            $query->where('attendance_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('attendance_date', '<=', $request->end_date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $attendance = $query->orderBy('attendance_date', 'desc')->paginate($request->get('per_page', 15));

        return response()->json($attendance);
    }

    public function studentClockInOut(Request $request, int $studentId): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->canViewStudent($user, $studentId)) {
            return response()->json(['error' => 'Unauthorized - student not under your supervision'], 403);
        }

        $query = ClockInOut::with(['internship'])->where('user_id', $studentId);

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json($records);
    }

    public function studentSummary(int $studentId): JsonResponse
    {
        $user = auth('api')->user();

        if (!$this->canViewStudent($user, $studentId)) {
            return response()->json(['error' => 'Unauthorized - student not under your supervision'], 403);
        }

        $student = User::with(['classes', 'school', 'internships.company', 'internships.supervisor'])->findOrFail($studentId);

        $totalLogs = DailyLog::where('student_id', $studentId)->count();
        $totalAttendance = Attendance::where('student_id', $studentId)->count();
        $presentCount = Attendance::where('student_id', $studentId)->where('status', 'present')->count();
        $absentCount = Attendance::where('student_id', $studentId)->where('status', 'absent')->count();
        $permissionCount = Attendance::where('student_id', $studentId)->where('status', 'permission')->count();

        return response()->json([
            'student' => $student,
            'summary' => [
                'total_daily_logs' => $totalLogs,
                'total_attendance_records' => $totalAttendance,
                'present' => $presentCount,
                'absent' => $absentCount,
                'permission' => $permissionCount,
                'attendance_percentage' => $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 2) : 0,
            ],
        ]);
    }

    private function canViewStudent($user, int $studentId): bool
    {
        if ($user->isSchoolAdmin()) {
            return true;
        }

        $studentIds = $user->getMyStudents()->pluck('id');
        return $studentIds->contains($studentId);
    }
}
