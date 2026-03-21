<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InternshipController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Internship::with(['student', 'company', 'supervisor']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $internships = $query->paginate($request->get('per_page', 15));

        return response()->json($internships);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        $activeInternship = Internship::where('student_id', $data['student_id'])
            ->where('status', 'active')
            ->first();

        if ($activeInternship) {
            return response()->json(['error' => 'Student already has an active internship'], 422);
        }

        $internship = Internship::create($data);

        return response()->json($internship, 201);
    }

    public function show(int $id): JsonResponse
    {
        $internship = Internship::with(['student', 'company', 'supervisor'])->findOrFail($id);

        return response()->json($internship);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $internship = Internship::findOrFail($id);

        $data = $request->validate([
            'company_id' => 'sometimes|required|exists:companies,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:active,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $internship->update($data);

        return response()->json($internship);
    }

    public function destroy(int $id): JsonResponse
    {
        $internship = Internship::findOrFail($id);
        $internship->delete();

        return response()->json(['message' => 'Internship deleted successfully']);
    }

    public function end(int $id): JsonResponse
    {
        $internship = Internship::findOrFail($id);
        $internship->update(['status' => 'completed']);

        return response()->json($internship);
    }

    public function studentHistory(int $studentId): JsonResponse
    {
        $student = User::findOrFail($studentId);
        $internships = Internship::with(['company', 'supervisor'])
            ->where('student_id', $studentId)
            ->get();

        return response()->json($internships);
    }

    public function batchStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'student_ids' => 'required|array|min:1|max:50',
            'company_id' => 'required|exists:companies,id',
            'supervisor_id' => 'nullable|exists:users,id,role,company_supervisor',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $studentIds = $data['student_ids'];
        $existingActive = Internship::whereIn('student_id', $studentIds)
            ->where('status', 'active')
            ->pluck('student_id')
            ->toArray();

        if (!empty($existingActive)) {
            $existingUsers = User::whereIn('id', $existingActive)->pluck('name')->toArray();
            return response()->json([
                'error' => 'The following students already have active internships: ' . implode(', ', $existingUsers)
            ], 422);
        }

        $internships = [];
        foreach ($studentIds as $studentId) {
            $internships[] = [
                'student_id' => $studentId,
                'company_id' => $data['company_id'],
                'supervisor_id' => $data['supervisor_id'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => 'active',
            ];
        }

        Internship::insert($internships);

        return response()->json(['message' => 'Internships created successfully', 'count' => count($internships)], 201);
    }
}
