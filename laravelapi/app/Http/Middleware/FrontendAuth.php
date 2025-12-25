<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontendAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthenticated'
            ], 401);
        }

        if (!$request->user()->currentAccessToken()?->can('frontend')) {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden'
            ], 403);
        }

        return $next($request);
    }
}








// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;

// class FrontendAuth
// {
//     public function handle(Request $request, Closure $next)
//     {
//         if (!$request->user()) {
//             return response()->json([
//                 'message' => 'Unauthenticated frontend user'
//             ], 401);
//         }

//         return $next($request);
//     }
// }

