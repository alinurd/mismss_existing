<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Static API key check for /api routes (no user/session involved).
 * Usage: ->middleware('api.key:tracking') or 'api.key:internal'
 * Keys come from config('services.api_keys.<scope>'), a comma-separated env list.
 * Client sends `X-API-Key: <key>` (or `?api_key=<key>`).
 */
class ApiKeyAuth
{
    public function handle(Request $request, Closure $next, string $scope)
    {
        $given = (string) ($request->header('X-API-Key') ?: $request->query('api_key', ''));
        $valid = array_filter(array_map('trim', explode(',', (string) config("services.api_keys.$scope"))));

        foreach ($valid as $key) {
            if ($given !== '' && hash_equals($key, $given)) {
                return $next($request);
            }
        }

        return response()->json(['status' => 401, 'message' => 'Invalid or missing API key'], 401);
    }
}
