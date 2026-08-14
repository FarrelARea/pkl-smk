<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InternshipController extends Controller
{
    private function ensureUserCanAccessStudent(Request $request, User $student): void
    {
        if ($student->role !== 'student' || !$student->belongsToAdminSchool($request->user())) {
            abort(response()->json(['error' => 'Forbidden - insufficient permissions'], 403));
        }
    }

    private function resolveAccessibleCompany(Request $request, int $companyId): Company
    {
        $company = Company::findOrFail($companyId);

        if (!$company->belongsToAdminSchool($request->user())) {
            abort(response()->json(['error' => 'Forbidden - insufficient permissions'], 403));
        }

        return $company;
    }

    private function ensureSupervisorMatchesCompany(?int $supervisorId, Company $company): void
    {
        if (!$supervisorId) {
            return;
        }

        $supervisor = User::where('role', 'company_supervisor')->findOrFail($supervisorId);

        if ($supervisor->company_id !== $company->id || $supervisor->school_id !== $company->school_id) {
            abort(response()->json(['error' => 'Supervisor must belong to the selected company'], 422));
        }
    }

    private function scopeQuery(Request $request)
    {
        $query = Internship::with(['student', 'company', 'supervisor']);

        if (!$request->user()->isSuperAdmin()) {
            $query->whereHas('student', function ($builder) use ($request) {
                $builder->where('school_id', $request->user()->school_id);
            });
        }

        return $query;
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->scopeQuery($request);

        if ($request->has('student_id')) {
            $student = User::findOrFail($request->student_id);
            $this->ensureUserCanAccessStudent($request, $student);
            $query->where('student_id', $student->id);
        }

        if ($request->has('company_id')) {
            $company = $this->resolveAccessibleCompany($request, (int) $request->company_id);
            $query->where('company_id', $company->id);
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

        $student = User::findOrFail($data['student_id']);
        $this->ensureUserCanAccessStudent($request, $student);
        $company = $this->resolveAccessibleCompany($request, $data['company_id']);
        $this->ensureSupervisorMatchesCompany($data['supervisor_id'] ?? null, $company);

        $activeInternship = Internship::where('student_id', $data['student_id'])
            ->where('status', 'active')
            ->first();

        if ($activeInternship) {
            return response()->json(['error' => 'Student already has an active internship'], 422);
        }

        $internship = Internship::create($data);

        return response()->json($internship->load(['student', 'company', 'supervisor']), 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $internship = $this->scopeQuery($request)->findOrFail($id);

        return response()->json($internship);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $internship = $this->scopeQuery($request)->findOrFail($id);

        $data = $request->validate([
            'company_id' => 'sometimes|required|exists:companies,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:active,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $company = isset($data['company_id'])
            ? $this->resolveAccessibleCompany($request, $data['company_id'])
            : $this->resolveAccessibleCompany($request, $internship->company_id);

        $this->ensureSupervisorMatchesCompany($data['supervisor_id'] ?? $internship->supervisor_id, $company);

        $internship->update($data);

        return response()->json($internship->fresh()->load(['student', 'company', 'supervisor']));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $internship = $this->scopeQuery($request)->findOrFail($id);
        $internship->delete();

        return response()->json(['message' => 'Internship deleted successfully']);
    }

    public function end(Request $request, int $id): JsonResponse
    {
        $internship = $this->scopeQuery($request)->findOrFail($id);
        $internship->update(['status' => 'completed']);

        return response()->json($internship);
    }

    public function studentHistory(Request $request, int $studentId): JsonResponse
    {
        $student = User::findOrFail($studentId);
        $this->ensureUserCanAccessStudent($request, $student);

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

        $company = $this->resolveAccessibleCompany($request, $data['company_id']);
        $this->ensureSupervisorMatchesCompany($data['supervisor_id'] ?? null, $company);

        $students = User::whereIn('id', $data['student_ids'])->get()->keyBy('id');
        foreach ($data['student_ids'] as $studentId) {
            $student = $students->get($studentId);
            if (!$student) {
                return response()->json(['error' => 'Student not found'], 422);
            }
            $this->ensureUserCanAccessStudent($request, $student);
        }

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
