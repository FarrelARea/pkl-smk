<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($request->user()->role !== $role && $request->user()->role !== 'superadmin') {
            return response()->json(['error' => 'Forbidden - insufficient permissions'], 403);
        }

        return $next($request);
    }
}
