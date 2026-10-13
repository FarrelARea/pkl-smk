<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendancePoint;
use App\Models\ClockInOut;
use App\Models\Internship;
use App\Models\User;
use App\Services\DistanceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected DistanceService $distanceService;

    public function __construct(DistanceService $distanceService)
    {
        $this->distanceService = $distanceService;
    }

    private function scopeQuery(Request $request): Builder
    {
        $query = Attendance::with(['student', 'internship']);

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
            $query->where('attendance_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('attendance_date', '<=', $request->end_date);
        }

        $attendance = $query->orderBy('attendance_date', 'desc')->paginate($request->get('per_page', 15));
        $attendance->getCollection()->transform(fn (Attendance $record) => $this->withClockData($record));

        return response()->json($attendance);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'internship_id' => 'required|exists:internships,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,absent,sick,permission',
            'notes' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $student = $this->ensureUserCanAccessStudent($request, (int) $data['student_id']);
        $internship = $this->resolveAccessibleInternship($request, (int) $data['internship_id']);

        if ($internship->student_id !== $student->id) {
            return response()->json(['error' => 'Internship does not belong to the selected student'], 422);
        }

        $data['student_id'] = $student->id;
        $data['internship_id'] = $internship->id;

        $existing = Attendance::where('student_id', $data['student_id'])
            ->where('attendance_date', $data['attendance_date'])
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Attendance already recorded for this date'], 422);
        }

        $data['recorded_by'] = auth('api')->id();
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
                $data['location_verified'] = $locationValidation['verified'];
                $data['location_distance'] = $locationValidation['distance'];
                $data['attendance_point_id'] = $locationValidation['point']->id;
            } elseif ($company->hasLocation()) {
                $locationValidation = $this->distanceService->validateLocation(
                    $data['latitude'],
                    $data['longitude'],
                    $company->latitude,
                    $company->longitude,
                    $company->distance_threshold ?? 100
                );
                $data['location_verified'] = $locationValidation['verified'];
                $data['location_distance'] = $locationValidation['distance'];
            } else {
                $data['location_verified'] = false;
            }
        } else {
            $data['location_verified'] = false;
        }

        $attendance = Attendance::create($data);

        $response = ['data' => $attendance];

        if ($locationValidation) {
            $response['location'] = [
                'verified' => $locationValidation['verified'],
                'distance' => $locationValidation['distance'],
                'message' => $locationValidation['message'],
            ];
        }

        return response()->json($response, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $attendance = $this->scopeQuery($request)
            ->with('recordedBy')
            ->findOrFail($id);

        return response()->json($this->withClockData($attendance));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->isSuperAdmin() && $request->user()->role !== 'school_admin') {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $attendance = $this->scopeQuery($request)->findOrFail($id);

        $data = $request->validate([
            'status' => 'sometimes|required|in:present,absent,sick,permission',
            'notes' => 'nullable|string',
            'clock_in' => 'sometimes|nullable|date',
            'clock_out' => 'sometimes|nullable|date',
            'location_verified' => 'sometimes|boolean',
        ]);

        $clocks = ClockInOut::query()
            ->where('user_id', $attendance->student_id)
            ->where('internship_id', $attendance->internship_id)
            ->whereIn('type', ['clock_in', 'clock_out'])
            ->whereDate('created_at', $attendance->attendance_date)
            ->orderBy('created_at')
            ->get();
        $existingIn = $clocks->firstWhere('type', 'clock_in');
        $existingOut = $clocks->firstWhere('type', 'clock_out');
        $clockIn = $request->has('clock_in')
            ? ($request->input('clock_in') === null ? null : Carbon::parse($request->input('clock_in')))
            : $existingIn?->created_at;
        $clockOut = $request->has('clock_out')
            ? ($request->input('clock_out') === null ? null : Carbon::parse($request->input('clock_out')))
            : $existingOut?->created_at;

        if ($clockIn && $clockOut && $clockOut->lt($clockIn)) {
            return response()->json(['error' => 'clock_out must not be earlier than clock_in'], 422);
        }

        $attendance->update(collect($data)->only(['status', 'notes', 'location_verified'])->all());

        foreach (['clock_in' => 'clock_in', 'clock_out' => 'clock_out'] as $input => $type) {
            if (!$request->has($input)) {
                continue;
            }

            $clock = ClockInOut::query()
                ->where('user_id', $attendance->student_id)
                ->where('internship_id', $attendance->internship_id)
                ->where('type', $type)
                ->whereDate('created_at', $attendance->attendance_date)
                ->orderBy('created_at')
                ->first();

            if ($request->input($input) === null) {
                $clock?->delete();
                continue;
            }

            if (!$clock) {
                continue;
            }
            $clock->created_at = Carbon::parse($request->input($input));
            if ($request->has('location_verified')) {
                $clock->is_within_range = $request->boolean('location_verified');
            }
            $clock->save();
        }

        if ($request->has('location_verified')) {
            ClockInOut::query()
                ->where('user_id', $attendance->student_id)
                ->where('internship_id', $attendance->internship_id)
                ->whereIn('type', ['clock_in', 'clock_out'])
                ->whereDate('created_at', $attendance->attendance_date)
                ->update(['is_within_range' => $request->boolean('location_verified')]);
        }

        return response()->json($this->withClockData($attendance->fresh()));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        if (!$request->user()->isSuperAdmin() && $request->user()->role !== 'school_admin') {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'internship_id' => 'required|exists:internships,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,absent,sick,permission',
            'notes' => 'nullable|string',
        ]);

        $student = $this->ensureUserCanAccessStudent($request, (int) $data['student_id']);
        $internship = $this->resolveAccessibleInternship($request, (int) $data['internship_id']);
        if ($internship->student_id !== $student->id) {
            return response()->json(['error' => 'Internship does not belong to the selected student'], 422);
        }

        $attendance = Attendance::updateOrCreate(
            ['student_id' => $student->id, 'attendance_date' => $data['attendance_date']],
            [
                'internship_id' => $internship->id,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'recorded_by' => auth('api')->id(),
            ],
        );

        return response()->json($this->withClockData($attendance));
    }

    private function withClockData(Attendance $attendance): Attendance
    {
        $clocks = ClockInOut::query()
            ->where('user_id', $attendance->student_id)
            ->where('internship_id', $attendance->internship_id)
            ->whereDate('created_at', $attendance->attendance_date)
            ->orderBy('created_at')
            ->get();
        $clockIn = $clocks->firstWhere('type', 'clock_in');
        $clockOut = $clocks->firstWhere('type', 'clock_out');

        return $attendance->setAttribute('clock_in_id', $clockIn?->id)
            ->setAttribute('clock_out_id', $clockOut?->id)
            ->setAttribute('clock_in', $clockIn?->created_at)
            ->setAttribute('clock_out', $clockOut?->created_at)
            ->setAttribute('location_verified', $clocks->isNotEmpty()
                ? $clocks->every(fn (ClockInOut $clock) => (bool) $clock->is_within_range)
                : (bool) $attendance->location_verified);
    }

    private function clockCoordinates(Attendance $attendance): ?array
    {
        if ($attendance->latitude !== null && $attendance->longitude !== null) {
            return [
                'latitude' => $attendance->latitude,
                'longitude' => $attendance->longitude,
            ];
        }

        $company = $attendance->loadMissing('internship.company')->internship?->company;
        if ($company?->latitude !== null && $company?->longitude !== null) {
            return [
                'latitude' => $company->latitude,
                'longitude' => $company->longitude,
            ];
        }

        return null;
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $attendance = $this->scopeQuery($request)->findOrFail($id);
        $attendance->delete();

        return response()->json(['message' => 'Attendance deleted successfully']);
    }

    public function bulkStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:users,id',
            'attendance.*.internship_id' => 'required|exists:internships,id',
            'attendance.*.attendance_date' => 'required|date',
            'attendance.*.status' => 'required|in:present,absent,sick,permission',
            'attendance.*.notes' => 'nullable|string',
            'attendance.*.latitude' => 'nullable|numeric|between:-90,90',
            'attendance.*.longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $created = 0;
        $failed = 0;
        $locationWarnings = [];

        foreach ($data['attendance'] as $item) {
            $student = $this->ensureUserCanAccessStudent($request, (int) $item['student_id']);
            $internship = $this->resolveAccessibleInternship($request, (int) $item['internship_id']);

            if ($internship->student_id !== $student->id) {
                $failed++;
                continue;
            }

            $item['student_id'] = $student->id;
            $item['internship_id'] = $internship->id;

            $existing = Attendance::where('student_id', $item['student_id'])
                ->where('attendance_date', $item['attendance_date'])
                ->first();

            if ($existing) {
                $failed++;
                continue;
            }

            $item['recorded_by'] = auth('api')->id();
            $company = $internship->company;

            if ($company && isset($item['latitude']) && isset($item['longitude'])) {
                $approvedPoints = AttendancePoint::approved()->forCompany($company->id)->get();

                if ($approvedPoints->isNotEmpty()) {
                    $validation = $this->distanceService->findNearestPoint(
                        $item['latitude'],
                        $item['longitude'],
                        $approvedPoints
                    );
                    $item['location_verified'] = $validation['verified'];
                    $item['location_distance'] = $validation['distance'];
                    $item['attendance_point_id'] = $validation['point']->id;
                } elseif ($company->hasLocation()) {
                    $validation = $this->distanceService->validateLocation(
                        $item['latitude'],
                        $item['longitude'],
                        $company->latitude,
                        $company->longitude,
                        $company->distance_threshold ?? 100
                    );
                    $item['location_verified'] = $validation['verified'];
                    $item['location_distance'] = $validation['distance'];
                } else {
                    $validation = null;
                    $item['location_verified'] = false;
                }

                if (isset($validation) && $validation && !$validation['verified']) {
                    $locationWarnings[] = "Student {$item['student_id']}: {$validation['message']}";
                }
            } else {
                $item['location_verified'] = false;
            }

            Attendance::create($item);
            $created++;
        }

        $response = [
            'message' => 'Bulk attendance recording completed',
            'created' => $created,
            'failed' => $failed,
        ];

        if (!empty($locationWarnings)) {
            $response['location_warnings'] = $locationWarnings;
        }

        return response()->json($response);
    }

    public function summary(int $studentId): JsonResponse
    {
        $query = Attendance::where('student_id', $studentId);

        $total = $query->count();
        $present = $query->where('status', 'present')->count();
        $absent = $query->where('status', 'absent')->count();
        $sick = $query->where('status', 'sick')->count();
        $permission = $query->where('status', 'permission')->count();

        return response()->json([
            'student_id' => $studentId,
            'total_days' => $total,
            'present' => $present,
            'absent' => $absent,
            'sick' => $sick,
            'permission' => $permission,
            'attendance_percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ]);
    }
}
