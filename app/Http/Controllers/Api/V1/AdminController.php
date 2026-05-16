<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $targetUser = User::findOrFail($data['user_id']);

        $requestingUser = $request->user();
        $allowedRoles = ['teacher', 'student', 'company_supervisor'];

        if ($requestingUser->isSuperAdmin()) {
            $allowedRoles[] = 'school_admin';
        }

        if (!in_array($targetUser->role, $allowedRoles, true)) {
            return response()->json(['error' => 'Cannot reset password for users with this role'], 403);
        }

        if (!$requestingUser->isSuperAdmin() && $targetUser->school_id !== $requestingUser->school_id) {
            return response()->json(['error' => 'Can only reset passwords for users in the same school'], 403);
        }

        if (!$requestingUser->isSuperAdmin() && $targetUser->role === 'school_admin') {
            return response()->json(['error' => 'Cannot reset password for school admin accounts'], 403);
        }

        // Reset the password
        // Note: The User model has 'hashed' password cast, so we assign directly
        $targetUser->password = $data['new_password'];
        $targetUser->save();

        return response()->json([
            'message' => 'Password reset successfully',
            'user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'role' => $targetUser->role,
            ],
        ]);
    }
}
