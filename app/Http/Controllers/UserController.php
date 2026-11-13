<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\OrganizationInvitation;
use App\Models\Role;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Organization $organization)
    {
        $members = $organization->members()
            ->with(['user', 'role', 'vendor'])
            ->orderBy('created_at')
            ->get();

        $invitations = $organization->invitations()
            ->with(['role', 'inviter'])
            ->latest()
            ->get();

        return view('users.index', compact(
            'organization',
            'members',
            'invitations'
        ));
    }

    public function edit(
        Organization $organization,
        OrganizationMember $member
    ) {
        if ($member->role?->role_name === 'Admin') {
            abort(403, 'Role Admin tidak dapat diubah melalui halaman ini.');
        }

        $roles = Role::whereIn('role_name', [
            'Staf Operasional',
            'Vendor',
        ])->orderBy('role_name')->get();

        return view('users.edit', compact(
            'organization',
            'member',
            'roles'
        ));
    }

    public function update(
        Request $request,
        Organization $organization,
        OrganizationMember $member
    ) {
        if ($member->role?->role_name === 'Admin') {
            abort(403, 'Role Admin tidak dapat diubah melalui halaman ini.');
        }

        $validated = $request->validate([
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
        ]);

        $role = Role::whereIn('role_name', [
            'Staf Operasional',
            'Vendor',
        ])->findOrFail($validated['role_id']);

        $vendorId = null;

        if ($role->role_name === 'Vendor') {
            $vendor = Vendor::where('organization_id', $organization->id)
                ->whereRaw('LOWER(email) = ?', [
                    strtolower($member->user->email),
                ])
                ->first();

            if (!$vendor) {
                return back()->withErrors([
                    'role_id' => 'Tidak ditemukan profil Vendor dalam organisasi ini dengan email yang cocok. Buat atau periksa profil Vendor terlebih dahulu.',
                ])->withInput();
            }

            $alreadyUsed = $organization->members()
                ->where('vendor_id', $vendor->id)
                ->where('id', '!=', $member->id)
                ->exists();

            if ($alreadyUsed) {
                return back()->withErrors([
                    'role_id' => 'Profil Vendor tersebut sudah terhubung dengan anggota lain.',
                ])->withInput();
            }

            $vendorId = $vendor->id;
        }

        $member->update([
            'role_id' => $role->id,
            'vendor_id' => $vendorId,
        ]);

        return redirect()
            ->route('users.index', $organization->slug)
            ->with('success', 'Role anggota berhasil diperbarui.');
    }

    public function revokeInvitation(
        Organization $organization,
        OrganizationInvitation $invitation
    ) {
        if (
            $invitation->accepted_at ||
            $invitation->revoked_at
        ) {
            return back()->withErrors([
                'invitation' => 'Undangan ini sudah tidak dapat dibatalkan.',
            ]);
        }

        $invitation->update([
            'revoked_at' => now(),
        ]);

        return back()->with(
            'success',
            'Undangan berhasil dibatalkan.'
        );
    }
}
