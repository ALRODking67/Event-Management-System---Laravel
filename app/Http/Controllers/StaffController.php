<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Organization $organization)
    {
        $staffMembers = $organization->members()
            ->with(['user', 'role'])
            ->whereHas('role', function ($query) {
                $query->where('role_name', 'Staf Operasional');
            })
            ->orderBy('created_at')
            ->get();

        return view('staff.index', compact(
            'organization',
            'staffMembers'
        ));
    }

    public function create(Organization $organization)
    {
        return view('staff.create', compact('organization'));
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        DB::transaction(function () use ($validated, $organization) {
            $staffRole = Role::where(
                'role_name',
                'Staf Operasional'
            )->firstOrFail();

            $user = User::create([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'password' => $validated['password'],
                'role_id' => $staffRole->id,
                'is_active' => true,
                'vendor_id' => null,
            ]);

            OrganizationMember::create([
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'role_id' => $staffRole->id,
                'vendor_id' => null,
            ]);
        });

        return redirect()
            ->route('staff.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Akun Staf Operasional berhasil dibuat.');
    }

    public function edit(
        Organization $organization,
        OrganizationMember $member
    ) {
        $this->ensureStaffMember($organization, $member);

        $member->load(['user', 'role']);

        return view('staff.edit', compact(
            'organization',
            'member'
        ));
    }

    public function update(
        Request $request,
        Organization $organization,
        OrganizationMember $member
    ) {
        $this->ensureStaffMember($organization, $member);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($member->user_id),
            ],
        ], [
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
        ]);

        $member->user->update([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
        ]);

        return redirect()
            ->route('staff.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Data staf berhasil diperbarui.');
    }

    private function ensureStaffMember(
        Organization $organization,
        OrganizationMember $member
    ): void {
        abort_unless(
            (int) $member->organization_id === (int) $organization->id
            && $member->role?->role_name === 'Staf Operasional',
            404
        );
    }
}
