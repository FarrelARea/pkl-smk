<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AttendancePoint;
use App\Models\DailyLog;
use App\Models\Internship;
use App\Models\User;
use App\Services\DistanceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyLogController extends Controller
{
    protected DistanceService $distanceService;

    public function __construct(DistanceService $distanceService)
    {
        $this->distanceService = $distanceService;
    }

    private function scopeQuery(Request $request): Builder
    {
        $query = DailyLog::with(['student', 'internship']);

        if (!$request->user()->isSuperAdmin()) {
            $query->whereHas('student', function (Builder $builder) use ($request) {
                $builder->where('school_id', $request->user()->school_id);
            });
        }

        return $query;
    }

    private function ensureUserCanAccessStudent(Request $request, int $studentId): User
    {
        $student = User::findOrFail($studentId);

        if ($student->role !== 'student' || !$student->belongsToAdminSchool($request->user())) {
            abort(response()->json(['error' => 'Forbidden - insufficient permissions'], 403));
        }

        return $student;
    }

    private function resolveAccessibleInternship(Request $request, int $internshipId): Internship
    {
        $internship = Internship::with(['student', 'company'])->findOrFail($internshipId);

        if (!$internship->student || !$internship->student->belongsToAdminSchool($request->user())) {
            abort(response()->json(['error' => 'Forbidden - insufficient permissions'], 403));
        }

        return $internship;
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->scopeQuery($request);

        if ($request->has('student_id')) {
            $student = $this->ensureUserCanAccessStudent($request, (int) $request->student_id);
            $query->where('student_id', $student->id);
        }

        if ($request->has('internship_id')) {
            $internship = $this->resolveAccessibleInternship($request, (int) $request->internship_id);
            $query->where('internship_id', $internship->id);
        }

        if ($request->has('start_date')) {
            $query->where('log_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('log_date', '<=', $request->end_date);
        }

        $logs = $query->orderBy('log_date', 'desc')->paginate($request->get('per_page', 15));

        return response()->json($logs);
    }

    public function store(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $isStudentCreatingOwnLog = $user->isStudent();

        $rules = [
            'internship_id' => 'required|exists:internships,id',
            'log_date' => 'required|date|before_or_equal:today',
            'activities' => 'required|string',
            'reflection' => 'nullable|string',
            'context' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];

        if (!$isStudentCreatingOwnLog) {
            $rules['student_id'] = 'required|exists:users,id';
        }

        $data = $request->validate($rules);

        $logDate = now()->parse($data['log_date']);
        $maxPastDate = now()->subDays(7);

        if ($logDate->lt($maxPastDate)) {
            return response()->json(['error' => 'Cannot create log for date more than 7 days in the past'], 422);
        }

        $student = $isStudentCreatingOwnLog
            ? $user
            : $this->ensureUserCanAccessStudent($request, (int) $data['student_id']);
        $studentId = $student->id;

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('daily-log-photos', 'public');
        }

        $internship = $this->resolveAccessibleInternship($request, (int) $data['internship_id']);

        if ($internship->student_id !== $studentId) {
            return response()->json(['error' => 'Internship does not belong to the selected student'], 422);
        }

        $company = $internship->company;

        $locationValidation = null;
        if ($company && isset($data['latitude']) && isset($data['longitude'])) {
            $approvedPoints = AttendancePoint::approved()->forCompany($company->id)->get();

            if ($approvedPoints->isNotEmpty()) {
                $locationValidation = $this->distanceService->findNearestPoint(
                    $data['latitude'],
                    $data['longitude'],
                    $approvedPoints
                );
            } elseif ($company->hasLocation()) {
                $locationValidation = $this->distanceService->validateLocation(
                    $data['latitude'],
                    $data['longitude'],
                    $company->latitude,
                    $company->longitude,
                    $company->distance_threshold ?? 100
                );
            }
        }

        $logData = [
            'student_id' => $studentId,
            'internship_id' => $internship->id,
            'log_date' => $data['log_date'],
            'activities' => $data['activities'],
            'reflection' => $data['reflection'] ?? null,
            'context' => $data['context'] ?? null,
            'photo' => $photoPath,
            'location_verified' => $locationValidation ? $locationValidation['verified'] : false,
            'location_distance' => $locationValidation ? $locationValidation['distance'] : null,
        ];

        if (isset($data['latitude'])) {
            $logData['latitude'] = $data['latitude'];
        }

        if (isset($data['longitude'])) {
            $logData['longitude'] = $data['longitude'];
        }

        $log = DailyLog::create($logData);

        $response = [
            'message' => 'Daily log created successfully',
            'daily_log' => $log->load('student', 'internship'),
        ];

        if ($locationValidation && !$locationValidation['verified']) {
            $response['warning'] = $locationValidation['message'];
        }

        return response()->json($response, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $log = $this->scopeQuery($request)
            ->with(['teacher', 'comments.author'])
            ->findOrFail($id);

        return response()->json($log);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $log = $this->scopeQuery($request)->findOrFail($id);

        if (!$log->canEdit()) {
            return response()->json(['error' => 'Update window has expired (24 hours)'], 403);
        }

        $data = $request->validate([
            'activities' => 'sometimes|required|string',
            'reflection' => 'nullable|string',
            'context' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('daily-log-photos', 'public');
        }

        $log->update($data);

        return response()->json($log);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $log = $this->scopeQuery($request)->findOrFail($id);

        if (!$log->canEdit()) {
            return response()->json(['error' => 'Delete window has expired (24 hours)'], 403);
        }

        $log->delete();

        return response()->json(['message' => 'Daily log deleted successfully']);
    }

    public function addComment(Request $request, int $id): JsonResponse
    {
        $log = $this->scopeQuery($request)->findOrFail($id);

        $data = $request->validate([
            'teacher_comment' => 'required_without:comment|string',
            'comment' => 'required_without:teacher_comment|string',
        ]);

        $log->update([
            'teacher_comment' => $data['teacher_comment'] ?? $data['comment'],
            'teacher_id' => auth('api')->id(),
        ]);

        return response()->json($log);
    }
}
