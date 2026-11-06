<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = auth()->user()
            ->organizationMemberships()
            ->with(['organization', 'role'])
            ->get();

        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $adminRole = Role::where('role_name', 'Admin')->first();

        if (!$adminRole) {
            return back()->withErrors([
                'name' => 'Role Admin belum tersedia di database.',
            ])->withInput();
        }

        $organization = DB::transaction(function () use ($validated, $adminRole) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug . '-' . Str::lower(Str::random(6));

            $organization = Organization::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'created_by' => auth()->id(),
            ]);

            $organization->members()->create([
                'user_id' => auth()->id(),
                'role_id' => $adminRole->id,
            ]);

            return $organization;
        });

        return redirect()
            ->route('organizations.workspace', $organization->slug)
            ->with('success', 'Organisasi berhasil dibuat.');
    }

    public function workspace(Organization $organization)
    {
        $membership = request()->attributes->get('organizationMembership');

        $organization->loadCount(['members', 'events']);

        return view('organizations.workspace', compact(
            'organization',
            'membership'
        ));
    }
}