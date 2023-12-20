<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {   
        $token = $request->bearerToken();

        $claims = JWTAuth::getPayload($token)->toArray();

        $userRole = is_array($claims['role']) ? $claims['role'][0] : $claims['role'];

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have the right access to perform this action'
            ]);
        }
        
        return $next($request);
    }
}
