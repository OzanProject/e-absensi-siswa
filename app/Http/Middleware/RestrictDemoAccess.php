<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictDemoAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_demo) {
            // Allow only GET and HEAD requests
            if (!in_array($request->method(), ['GET', 'HEAD'])) {
                
                // Exception: Allow Logout
                if ($request->routeIs('logout')) {
                    return $next($request);
                }

                // Return error response
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Mode Demo: Aksi ini dinonaktifkan untuk tujuan demonstrasi.'
                    ], 403);
                }

                return redirect()->back()->with('error', 'Mode Demo: Aksi ini dinonaktifkan untuk tujuan demonstrasi.');
            }
        }

        return $next($request);
    }
}
