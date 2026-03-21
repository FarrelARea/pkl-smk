<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *      title="Internship API",
 *      version="1.0",
 *      description="API for School Internship Management System"
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8000/api/v1",
 *      description="Local API Server"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/auth/login",
     *      operationId="login",
     *      tags={"Authentication"},
     *      summary="User login",
     *      description="Returns JWT token on success",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", format="email", example="admin@school.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful login",
     *          @OA\JsonContent(
     *              @OA\Property(property="access_token", type="string"),
     *              @OA\Property(property="token_type", type="string", example="bearer"),
     *              @OA\Property(property="expires_in", type="integer")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid credentials"
     *      )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *      path="/auth/register",
     *      operationId="register",
     *      tags={"Authentication"},
     *      summary="User registration",
     *      description="Register a new user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "email", "password"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john@school.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *              @OA\Property(property="role", type="string", enum={"school_admin", "teacher", "student", "company_supervisor"}, example="student"),
     *              @OA\Property(property="school_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User created successfully"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|in:school_admin,teacher,student,company_supervisor',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['role'] = $data['role'] ?? 'student';

        $user = User::create($data);

        $token = auth('api')->login($user);

        return $this->respondWithToken($token, 201);
    }

    /**
     * @OA\Get(
     *      path="/auth/me",
     *      operationId="me",
     *      tags={"Authentication"},
     *      summary="Get current user",
     *      description="Returns the currently authenticated user",
     *      security={"bearerAuth": {}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function me(): JsonResponse
    {
        return response()->json(auth('api')->user());
    }

    /**
     * @OA\Post(
     *      path="/auth/logout",
     *      operationId="logout",
     *      tags={"Authentication"},
     *      summary="User logout",
     *      description="Invalidates the current JWT token",
     *      security={"bearerAuth": {}},
     *      @OA\Response(
     *          response=200,
     *          description="Successfully logged out"
     *      )
     * )
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Post(
     *      path="/auth/refresh",
     *      operationId="refresh",
     *      tags={"Authentication"},
     *      summary="Refresh JWT token",
     *      description="Returns a new JWT token",
     *      security={"bearerAuth": {}},
     *      @OA\Response(
     *          response=200,
     *          description="Token refreshed successfully"
     *      )
     * )
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken(string $token, int $status = 200): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], $status);
    }
}
