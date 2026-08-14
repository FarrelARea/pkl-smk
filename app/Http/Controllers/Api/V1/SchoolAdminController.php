<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SchoolAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['error' => 'Forbidden - only superadmin can manage school admins'], 403);
        }

        $query = User::with('school')->where('role', 'school_admin');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->integer('school_id'));
        }

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request): JsonResponse
    {
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['error' => 'Forbidden - only superadmin can manage school admins'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'school_id' => 'required|exists:schools,id',
        ]);

        $admin = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'school_admin',
            'school_id' => $data['school_id'],
        ]);

        return response()->json($admin->load('school'), 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['error' => 'Forbidden - only superadmin can manage school admins'], 403);
        }

        $admin = User::with('school')->where('role', 'school_admin')->findOrFail($id);

        return response()->json($admin);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['error' => 'Forbidden - only superadmin can manage school admins'], 403);
        }

        $admin = User::where('role', 'school_admin')->findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'school_id' => 'sometimes|required|exists:schools,id',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $admin->update($data);

        return response()->json($admin->load('school'));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['error' => 'Forbidden - only superadmin can manage school admins'], 403);
        }

        $admin = User::where('role', 'school_admin')->findOrFail($id);
        $admin->delete();

        return response()->json(['message' => 'School admin deleted successfully']);
    }
}
