<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        $user = auth()->user();
        
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'error' => 'Forbidden. Required role: ' . implode(' or ', $roles)
            ], 403);
        }
        
        return $next($request);
    }
}
