<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SupervisorController extends Controller
{
    private function resolveAccessibleCompany(Request $request, int $companyId): Company
    {
        $company = Company::findOrFail($companyId);

        if (!$company->belongsToAdminSchool($request->user())) {
            abort(response()->json(['error' => 'Forbidden - insufficient permissions'], 403));
        }

        return $company;
    }

    private function resolveAccessibleSupervisor(Request $request, int $id): User
    {
        $supervisor = User::with('company')->where('role', 'company_supervisor')->findOrFail($id);

        if (!$supervisor->belongsToAdminSchool($request->user())) {
            abort(response()->json(['error' => 'Forbidden - insufficient permissions'], 403));
        }

        return $supervisor;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = User::with('company')
            ->where('role', 'company_supervisor')
            ->accessibleByAdmin($user);

        if ($request->has('company_id')) {
            $company = $this->resolveAccessibleCompany($request, (int) $request->company_id);
            $query->where('company_id', $company->id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $supervisors = $query->paginate($request->get('per_page', 15));

        return response()->json($supervisors);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = $this->resolveAccessibleCompany($request, $data['company_id']);

        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'company_supervisor';
        $data['school_id'] = $company->school_id;

        $supervisor = User::create($data);

        return response()->json($supervisor->load('company'), 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $supervisor = $this->resolveAccessibleSupervisor($request, $id);

        return response()->json($supervisor);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $supervisor = $this->resolveAccessibleSupervisor($request, $id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($supervisor->company) {
            $data['school_id'] = $supervisor->company->school_id;
        }

        $supervisor->update($data);

        return response()->json($supervisor->fresh()->load('company'));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $supervisor = $this->resolveAccessibleSupervisor($request, $id);
        $supervisor->delete();

        return response()->json(['message' => 'Supervisor deleted successfully']);
    }

    public function assignCompany(Request $request, int $id): JsonResponse
    {
        $supervisor = $this->resolveAccessibleSupervisor($request, $id);

        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = $this->resolveAccessibleCompany($request, $data['company_id']);

        $supervisor->update([
            'company_id' => $company->id,
            'school_id' => $company->school_id,
        ]);

        return response()->json(['message' => 'Supervisor assigned to company']);
    }
}
