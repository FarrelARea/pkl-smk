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
        $query = Company::query();

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
        $data = $request->validate([
            'name' => 'required|string|max:255',
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

        $company = Company::create($data);

        return response()->json([
            'data' => $company,
            'message' => 'Company created successfully'
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $company = Company::with('supervisors', 'internships')->findOrFail($id);

        return response()->json([
            'data' => $company,
            'has_location' => $company->hasLocation(),
            'full_address' => $company->full_address,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $company = Company::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
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

        $company->update($data);

        return response()->json([
            'data' => $company,
            'message' => 'Company updated successfully'
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }

    public function search(Request $request): JsonResponse
    {
        $query = Company::query();

        if ($request->has('q') && $request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('industry', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->has('industry') && $request->industry) {
            $query->where('industry', $request->industry);
        }

        $companies = $query->select('id', 'name', 'industry', 'address', 'latitude', 'longitude', 'distance_threshold')
            ->paginate($request->get('per_page', 15));

        return response()->json($companies);
    }

    public function assignSupervisor(Request $request, int $id): JsonResponse
    {
        $company = Company::findOrFail($id);

        $data = $request->validate([
            'supervisor_id' => 'required|exists:users,id,role,company_supervisor',
        ]);

        $supervisor = User::where('role', 'company_supervisor')->findOrFail($data['supervisor_id']);
        $supervisor->update(['company_id' => $company->id]);

        return response()->json(['message' => 'Supervisor assigned to company']);
    }
}
