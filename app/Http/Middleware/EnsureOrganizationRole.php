<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $role = $request->attributes->get('organizationRole');

        if (!$role || !in_array($role, $roles, true)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses fitur ini.');
        }

        return $next($request);
    }
}