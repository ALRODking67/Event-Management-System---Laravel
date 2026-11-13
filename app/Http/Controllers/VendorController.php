<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    public function index(Organization $organization)
    {
        $vendors = $organization->vendors()
            ->with(['user', 'eventAssignments'])
            ->withCount('eventAssignments')
            ->orderBy('vendor_name')
            ->get();

        return view('vendors.index', compact('vendors', 'organization'));
    }

    public function create(Organization $organization)
    {
        return view('vendors.create', compact('organization'));
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'vendor_name' => ['required', 'string', 'max:150'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
            'service_type' => ['required', 'string', 'max:100'],
        ], [
            'email.required' => 'Email wajib diisi untuk membuat akun Vendor.',
            'email.unique' => 'Email tersebut sudah digunakan akun lain.',
            'password.required' => 'Password awal wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        DB::transaction(function () use ($validated, $organization) {
            $vendorRole = Role::where('role_name', 'Vendor')->firstOrFail();

            // 1. Buat profil Vendor.
            $vendor = $organization->vendors()->create([
                'vendor_name' => $validated['vendor_name'],
                'contact_name' => $validated['contact_name'] ?? null,
                'email' => strtolower($validated['email']),
                'phone' => $validated['phone'] ?? null,
                'service_type' => $validated['service_type'],
            ]);

            // 2. Buat akun login Vendor.
            // Password otomatis di-hash oleh cast pada model User.
            $user = User::create([
                'name' => $validated['contact_name']
                    ?: $validated['vendor_name'],
                'email' => strtolower($validated['email']),
                'password' => $validated['password'],
                'role_id' => $vendorRole->id,
                'is_active' => true,
                'vendor_id' => $vendor->id,
            ]);

            // 3. Hubungkan akun ke organisasi sebagai Vendor.
            OrganizationMember::create([
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'role_id' => $vendorRole->id,
                'vendor_id' => $vendor->id,
            ]);
        });

        return redirect()
            ->route('vendors.index', ['organization' => $organization->slug])
            ->with('success', 'Vendor dan akun login berhasil dibuat.');
    }

    public function edit(Organization $organization, Vendor $vendor)
    {
        abort_unless($vendor->organization_id === $organization->id, 404);

        return view('vendors.edit', compact('vendor', 'organization'));
    }

    public function update(
        Request $request,
        Organization $organization,
        Vendor $vendor
    ) {
        abort_unless($vendor->organization_id === $organization->id, 404);

        $vendor->load('user');

        $validated = $request->validate([
            'vendor_name' => ['required', 'string', 'max:150'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($vendor->user?->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'service_type' => ['required', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($validated, $vendor) {
            $email = strtolower($validated['email']);

            $vendor->update([
                'vendor_name' => $validated['vendor_name'],
                'contact_name' => $validated['contact_name'] ?? null,
                'email' => $email,
                'phone' => $validated['phone'] ?? null,
                'service_type' => $validated['service_type'],
            ]);

            // Sinkronkan identitas akun jika Vendor sudah memiliki akun.
            if ($vendor->user) {
                $vendor->user->update([
                    'name' => $validated['contact_name']
                        ?: $validated['vendor_name'],
                    'email' => $email,
                ]);
            }
        });

        return redirect()
            ->route('vendors.index', ['organization' => $organization->slug])
            ->with('success', 'Data Vendor berhasil diperbarui.');
    }
}
