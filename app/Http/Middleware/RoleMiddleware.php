<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan pengguna sudah login.
        if (!$request->user()) {
            abort(401);
        }

        // Ambil nama role pengguna yang sedang login.
        $userRole = $request->user()->role?->role_name;

        // Periksa apakah role pengguna diizinkan.
        if (!$userRole || !in_array($userRole, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
