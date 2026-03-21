<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ClockInOut;
use App\Models\Internship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClockInOutController extends Controller
{
    protected const MAX_DISTANCE_METERS = 20;

    public function index(Request $request): JsonResponse
    {
        $query = ClockInOut::with(['user', 'internship']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('internship_id')) {
            $query->where('internship_id', $request->internship_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json($records);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'internship_id' => 'required|exists:internships,id',
            'type' => 'required|in:clock_in,clock_out',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $userId = auth('api')->id();
        $internship = Internship::with('company')->findOrFail($data['internship_id']);

        if ($internship->student_id !== $userId) {
            return response()->json(['error' => 'Unauthorized - you are not assigned to this internship'], 403);
        }

        if ($internship->status !== 'active') {
            return response()->json(['error' => 'Internship is not active'], 400);
        }

        $company = $internship->company;
        $isWithinRange = false;
        $distance = null;
        $companyLat = $company->latitude ?? null;
        $companyLng = $company->longitude ?? null;

        if ($companyLat && $companyLng) {
            $distance = ClockInOut::calculateDistance(
                $data['latitude'],
                $data['longitude'],
                $companyLat,
                $companyLng
            );
            $isWithinRange = $distance <= self::MAX_DISTANCE_METERS;
        }

        // For clock_out, verify there's an unpaired clock_in (may be from previous day for night shifts)
        if ($data['type'] === 'clock_out') {
            $lastClockIn = ClockInOut::where('user_id', $userId)
                ->where('internship_id', $data['internship_id'])
                ->where('type', 'clock_in')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastClockIn) {
                return response()->json(['error' => 'Harus clock in terlebih dahulu'], 422);
            }

            // Check if that clock_in already has a matching clock_out after it
            $matchingClockOut = ClockInOut::where('user_id', $userId)
                ->where('internship_id', $data['internship_id'])
                ->where('type', 'clock_out')
                ->where('created_at', '>', $lastClockIn->created_at)
                ->first();

            if ($matchingClockOut) {
                // Last session already closed — this is a new clock_out without clock_in
                return response()->json(['error' => 'Harus clock in terlebih dahulu sebelum clock out'], 422);
            }
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('clock-photos', 'public');
        }

        $clockRecord = ClockInOut::create([
            'user_id' => $userId,
            'internship_id' => $data['internship_id'],
            'type' => $data['type'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'company_latitude' => $companyLat,
            'company_longitude' => $companyLng,
            'distance_meters' => $distance,
            'is_within_range' => $isWithinRange,
            'notes' => $data['notes'] ?? null,
            'photo' => $photoPath,
        ]);

        $message = $data['type'] === 'clock_in' ? 'Clocked in successfully' : 'Clocked out successfully';

        if (!$isWithinRange && $companyLat && $companyLng) {
            $message .= ' - Warning: You are ' . round($distance) . 'm away from company (max: ' . self::MAX_DISTANCE_METERS . 'm)';
        }

        return response()->json([
            'message' => $message,
            'clock_record' => $clockRecord->load('internship.company'),
            'distance_meters' => $distance,
            'is_within_range' => $isWithinRange,
            'max_distance_meters' => self::MAX_DISTANCE_METERS,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $record = ClockInOut::with(['user', 'internship.company'])->findOrFail($id);

        return response()->json($record);
    }

    public function todayStatus(int $internshipId): JsonResponse
    {
        $userId = auth('api')->id();

        $internship = Internship::with('company')->findOrFail($internshipId);

        if ($internship->student_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get all records today
        $todayRecords = ClockInOut::where('user_id', $userId)
            ->where('internship_id', $internshipId)
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('created_at')
            ->get();

        // Find the latest clock_in (could be from yesterday for night shifts)
        $lastClockIn = ClockInOut::where('user_id', $userId)
            ->where('internship_id', $internshipId)
            ->where('type', 'clock_in')
            ->orderBy('created_at', 'desc')
            ->first();

        // Check if there's a clock_out after the last clock_in
        $lastClockOut = null;
        $isCurrentlyIn = false;
        if ($lastClockIn) {
            $lastClockOut = ClockInOut::where('user_id', $userId)
                ->where('internship_id', $internshipId)
                ->where('type', 'clock_out')
                ->where('created_at', '>', $lastClockIn->created_at)
                ->orderBy('created_at', 'desc')
                ->first();
            $isCurrentlyIn = !$lastClockOut;
        }

        return response()->json([
            'date' => now()->toDateString(),
            'internship_id' => $internshipId,
            'is_currently_in' => $isCurrentlyIn,
            'clocked_in' => $todayRecords->where('type', 'clock_in')->count() > 0,
            'clocked_out' => $todayRecords->where('type', 'clock_out')->count() > 0,
            'last_clock_in' => $lastClockIn,
            'last_clock_out' => $lastClockOut,
            'today_records' => $todayRecords,
            'total_today' => $todayRecords->count(),
            'company_location' => [
                'latitude' => $internship->company->latitude ?? null,
                'longitude' => $internship->company->longitude ?? null,
            ],
        ]);
    }

    public function history(int $studentId, Request $request): JsonResponse
    {
        $query = ClockInOut::with(['internship.company'])
            ->where('user_id', $studentId);

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->has('internship_id')) {
            $query->where('internship_id', $request->internship_id);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json($records);
    }

    public function destroy(int $id): JsonResponse
    {
        $userId = auth('api')->id();
        $record = ClockInOut::findOrFail($id);

        if ($record->user_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($record->created_at->lt(now()->startOfDay())) {
            return response()->json(['error' => 'Cannot delete records from previous days'], 403);
        }

        $record->delete();

        return response()->json(['message' => 'Record deleted successfully']);
    }
}
