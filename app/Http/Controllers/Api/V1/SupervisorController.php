<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SupervisorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with('company')->where('role', 'company_supervisor');

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
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

        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'company_supervisor';

        $supervisor = User::create($data);

        return response()->json($supervisor, 201);
    }

    public function show(int $id): JsonResponse
    {
        $supervisor = User::findOrFail($id);

        return response()->json($supervisor);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $supervisor = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $supervisor->update($data);

        return response()->json($supervisor);
    }

    public function destroy(int $id): JsonResponse
    {
        $supervisor = User::findOrFail($id);
        $supervisor->delete();

        return response()->json(['message' => 'Supervisor deleted successfully']);
    }

    public function assignCompany(Request $request, int $id): JsonResponse
    {
        $supervisor = User::where('role', 'company_supervisor')->findOrFail($id);

        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $supervisor->update(['company_id' => $data['company_id']]);

        return response()->json(['message' => 'Supervisor assigned to company']);
    }
}
