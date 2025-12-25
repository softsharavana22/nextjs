<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$request->user()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthenticated'
            ], 401);
        }

        if (!$request->user()->currentAccessToken()?->can('admin')) {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden'
            ], 403);
        }

        // 🔐 Check token ability
        if (!$request->user()->tokenCan('admin')) {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden: Admin token required'
            ], 403);
        }        

        return $next($request);
    }
}
