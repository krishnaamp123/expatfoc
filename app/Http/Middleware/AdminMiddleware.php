<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->ajax()) {
            // Tangani permintaan AJAX
        }
        // List of roles that are allowed access
        $allowedRoles = ['super_admin', 'human_resource'];

        // Check if the user is authenticated and has one of the allowed roles
        if (Auth::check() && in_array(Auth::user()->role, $allowedRoles)) {
            return $next($request);
        }

        // If the user is not authorized error message
        return response()->json([
            "error" => "Access denied. You do not have the required permissions"
        ], 401);
    }
}
