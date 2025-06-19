<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role (passed from route)
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();

        if (!$user || $user->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only ' . $role . ' can access this resource.'
            ], 403);
        }

        return $next($request);
    }
}
