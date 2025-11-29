<?php

namespace App\Http\Middleware;

use App\ApiCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => ApiCode::UNAUTHORIZED,
                'errorCode' => 1,
                'data' => null,
                'message' => 'Unauthorized'
            ], 401);
        }

        if (!$user->isAdmin()) {
            return response()->json([
                'status' => ApiCode::FORBIDDEN,
                'errorCode' => 1,
                'data' => null,
                'message' => 'Access denied. Admin role required.'
            ], 403);
        }

        return $next($request);
    }
}
