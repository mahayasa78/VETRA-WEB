<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiKey;
use Illuminate\Support\Facades\Hash;

class CheckApiKey
{
    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        
        if (!$apiKey) {
            return response()->json(['error' => 'API Key required'], 401);
        }
        
        // Find by prefix (first 8 chars)
        $prefix = substr($apiKey, 0, 8);
        $keyRecord = ApiKey::where('key_prefix', $prefix)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();
        
        if (!$keyRecord || !hash_equals($keyRecord->key_hash, hash('sha256', $apiKey))) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }
        
        $keyRecord->update(['last_used_at' => now()]);
        
        // Set user dari API key
        auth()->setUser($keyRecord->user);
        
        return $next($request);
    }
}
