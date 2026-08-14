<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\PermissionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $query = PermissionRequest::with(['student', 'internship', 'handler']);

        if ($user->isTeacher()) {
            $studentIds = $user->getMyStudents()->pluck('id');
            $query->whereIn('student_id', $studentIds);
        } elseif ($user->isCompanySupervisor()) {
            $studentIds = $user->getMyStudents()->pluck('id');
            $query->whereIn('student_id', $studentIds);
        } elseif ($user->isStudent()) {
            $query->where('student_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json($records);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'internship_id' => 'required|exists:internships,id',
            'request_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:request_date',
            'type' => 'required|in:sick,permit,other',
            'reason' => 'required|string|max:1000',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $userId = auth('api')->id();

        $internship = Internship::findOrFail($data['internship_id']);

        if ($internship->student_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $photoPath = null;
        if ($request->hasFile('document')) {
            $photoPath = $request->file('document')->store('permission-documents', 'public');
        }

        $permissionRequest = PermissionRequest::create([
            'student_id' => $userId,
            'internship_id' => $data['internship_id'],
            'request_date' => $data['request_date'],
            'end_date' => $data['end_date'] ?? null,
            'type' => $data['type'],
            'reason' => $data['reason'],
            'document' => $photoPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Permission request submitted successfully',
            'permission_request' => $permissionRequest->load('student', 'internship'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = auth('api')->user();
        $permissionRequest = PermissionRequest::with(['student', 'internship.company', 'handler'])->findOrFail($id);

        if ($user->isTeacher()) {
            $studentIds = $user->getMyStudents()->pluck('id');
            if (!$studentIds->contains($permissionRequest->student_id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } elseif ($user->isCompanySupervisor()) {
            $studentIds = $user->getMyStudents()->pluck('id');
            if (!$studentIds->contains($permissionRequest->student_id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } elseif ($user->isStudent() && $permissionRequest->student_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($permissionRequest);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        return $this->handleRequest($request, $id, 'approve');
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        return $this->handleRequest($request, $id, 'reject');
    }

    private function handleRequest(Request $request, int $id, string $action): JsonResponse
    {
        $data = $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $user = auth('api')->user();
        $permissionRequest = PermissionRequest::findOrFail($id);

        if (!$permissionRequest->isPending()) {
            return response()->json(['error' => 'This request has already been processed'], 422);
        }

        if ($user->isTeacher()) {
            $studentIds = $user->getMyStudents()->pluck('id');
            if (!$studentIds->contains($permissionRequest->student_id)) {
                return response()->json(['error' => 'Unauthorized - student not in your class'], 403);
            }
        } elseif ($user->isCompanySupervisor()) {
            $studentIds = $user->getMyStudents()->pluck('id');
            if (!$studentIds->contains($permissionRequest->student_id)) {
                return response()->json(['error' => 'Unauthorized - student not under your supervision'], 403);
            }
        } else {
            return response()->json(['error' => 'Only teachers or supervisors can handle permission requests'], 403);
        }

        if ($action === 'approve') {
            $permissionRequest->approve($user->id, $data['note'] ?? null);
            $message = 'Permission request approved';
        } else {
            $permissionRequest->reject($user->id, $data['note'] ?? null);
            $message = 'Permission request rejected';
        }

        return response()->json([
            'message' => $message,
            'permission_request' => $permissionRequest->fresh()->load('student', 'internship', 'handler'),
        ]);
    }
}
