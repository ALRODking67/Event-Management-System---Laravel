<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $organization = $request->route('organization');
        $user = $request->user();

        if (!$organization || !$user) {
            abort(404);
        }

        // Pastikan parameter organization menjadi objek Model.
        if (!$organization instanceof Organization) {
            $organization = Organization::where('slug', $organization)
                ->firstOrFail();

            // Simpan objek agar middleware dan controller berikutnya
            // menerima objek Organization, bukan string slug.
            $request->route()->setParameter('organization', $organization);
        }

        $membership = $organization->members()
            ->with('role')
            ->where('user_id', $user->id)
            ->first();

        if (!$membership || !$membership->role) {
            abort(403, 'Anda bukan anggota organisasi ini atau role keanggotaan tidak valid.');
        }

        $roleName = $membership->role->role_name;

        $request->attributes->set('organization', $organization);
        $request->attributes->set('organizationMembership', $membership);
        $request->attributes->set('organizationRole', $roleName);

        view()->share('currentOrganization', $organization);
        view()->share('currentOrganizationRole', $roleName);

        return $next($request);
    }
}
