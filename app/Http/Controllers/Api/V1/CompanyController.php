<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Company::with('school')->accessibleByAdmin($user);

        if ($request->has('school_id') && $user->isSuperAdmin()) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('industry', 'like', '%' . $request->search . '%');
            });
        }

        $companies = $query->paginate($request->get('per_page', 15));

        return response()->json($companies);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'address' => 'nullable|string',
            'industry' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'distance_threshold' => 'nullable|integer|min:10|max:1000',
            'province_name' => 'nullable|string|max:100',
            'city_name' => 'nullable|string|max:100',
            'district_name' => 'nullable|string|max:100',
            'village_name' => 'nullable|string|max:100',
        ]);

        if (!$user->isSuperAdmin()) {
            $data['school_id'] = $user->school_id;
        }

        $company = Company::create($data);
        $company->load('school');

        return response()->json([
            'data' => $company,
            'message' => 'Company created successfully'
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $company = Company::with('school', 'supervisors', 'internships')->findOrFail($id);

        if (!$company->belongsToAdminSchool($request->user())) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        return response()->json([
            'data' => $company,
            'has_location' => $company->hasLocation(),
            'full_address' => $company->full_address,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $company = Company::findOrFail($id);

        if (!$company->belongsToAdminSchool($user)) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'school_id' => 'sometimes|required|exists:schools,id',
            'address' => 'nullable|string',
            'industry' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'distance_threshold' => 'nullable|integer|min:10|max:1000',
            'province_name' => 'nullable|string|max:100',
            'city_name' => 'nullable|string|max:100',
            'district_name' => 'nullable|string|max:100',
            'village_name' => 'nullable|string|max:100',
        ]);

        if (!$user->isSuperAdmin()) {
            $data['school_id'] = $user->school_id;
        }

        $company->update($data);
        $company->load('school');

        return response()->json([
            'data' => $company,
            'message' => 'Company updated successfully'
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $company = Company::findOrFail($id);

        if (!$company->belongsToAdminSchool($request->user())) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }

    public function search(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Company::query()->accessibleByAdmin($user);

        if ($request->has('school_id') && $user->isSuperAdmin()) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->has('q') && $request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('industry', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->has('industry') && $request->industry) {
            $query->where('industry', $request->industry);
        }

        $companies = $query->select('id', 'school_id', 'name', 'industry', 'address', 'latitude', 'longitude', 'distance_threshold')
            ->paginate($request->get('per_page', 15));

        return response()->json($companies);
    }

    public function assignSupervisor(Request $request, int $id): JsonResponse
    {
        $company = Company::findOrFail($id);

        if (!$company->belongsToAdminSchool($request->user())) {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        $data = $request->validate([
            'supervisor_id' => 'required|exists:users,id,role,company_supervisor',
        ]);

        $supervisor = User::where('role', 'company_supervisor')->findOrFail($data['supervisor_id']);
        $supervisor->update(['company_id' => $company->id]);

        return response()->json(['message' => 'Supervisor assigned to company']);
    }
}
