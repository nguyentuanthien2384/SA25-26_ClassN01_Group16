<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class GatewayTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $auth = $request->header('Authorization');

        // Check if Authorization header exists and starts with "Bearer "
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            Log::warning('[GATEWAY] Unauthorized: missing or malformed token', [
                'method' => $request->method(),
                'path'   => $request->path(),
                'ip'     => $request->ip(),
            ]);

            return response()->json([
                'error'   => 'Unauthorized',
                'details' => 'Authorization header missing or malformed',
            ], 401);
        }

        $token = trim(substr($auth, strlen('Bearer ')));

        // Validate token and assign role
        if ($token === 'valid-admin-token') {
            $request->attributes->set('role', 'admin');
            Log::info('[GATEWAY] Authenticated as admin', ['path' => $request->path()]);
            return $next($request);
        }

        if ($token === 'valid-user-token') {
            $request->attributes->set('role', 'user');
            Log::info('[GATEWAY] Authenticated as user', ['path' => $request->path()]);
            return $next($request);
        }

        // Invalid token
        Log::warning('[GATEWAY] Unauthorized: invalid token', [
            'method' => $request->method(),
            'path'   => $request->path(),
        ]);

        return response()->json([
            'error'   => 'Unauthorized',
            'details' => 'Invalid or expired token',
        ], 401);
    }
}
