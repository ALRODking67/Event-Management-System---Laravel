<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'vendor'])
            ->orderBy('name')
            ->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('role_name')->get();
        $vendors = Vendor::whereDoesntHave('user')
            ->orderBy('vendor_name')
            ->get();

        return view('users.create', compact('roles', 'vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
            'vendor_id' => [
                'nullable',
                'required_if:role_id,' . Role::where('role_name', 'Vendor')->value('id'),
                'exists:vendors,id',
                'unique:users,vendor_id',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
            'is_active' => ['required', 'boolean'],
        ]);

        $role = Role::findOrFail($validated['role_id']);

        if ($role->role_name !== 'Vendor') {
            $validated['vendor_id'] = null;
        }

        if ($role->role_name === 'Vendor' && empty($validated['vendor_id'])) {
            return back()
                ->withErrors(['vendor_id' => 'Akun Vendor wajib dihubungkan dengan profil Vendor.'])
                ->withInput();
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $role->id,
            'vendor_id' => $validated['vendor_id'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('role_name')->get();

        $vendors = Vendor::where(function ($query) use ($user) {
            $query->whereDoesntHave('user')
                ->orWhere('id', $user->vendor_id);
        })
            ->orderBy('vendor_name')
            ->get();

        return view('users.edit', compact('user', 'roles', 'vendors'));
    }

    public function update(Request $request, User $user)
    {
        $isSelf = $request->user()->id === $user->id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role_id' => ['required', 'exists:roles,id'],
            'vendor_id' => [
                'nullable',
                'exists:vendors,id',
                Rule::unique('users', 'vendor_id')->ignore($user->id),
            ],
            'is_active' => ['required', 'boolean'],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $role = Role::findOrFail($validated['role_id']);

        if ($isSelf) {
            if ((int) $validated['role_id'] !== (int) $user->role_id) {
                return back()
                    ->withErrors(['role_id' => 'Anda tidak dapat mengubah role akun sendiri.'])
                    ->withInput();
            }

            if (!$validated['is_active']) {
                return back()
                    ->withErrors(['is_active' => 'Anda tidak dapat menonaktifkan akun sendiri.'])
                    ->withInput();
            }
        }

        if ($role->role_name === 'Vendor') {
            if (empty($validated['vendor_id'])) {
                return back()
                    ->withErrors(['vendor_id' => 'Akun Vendor wajib dihubungkan dengan profil Vendor.'])
                    ->withInput();
            }
        } else {
            $validated['vendor_id'] = null;
        }

        if (
            $user->is_active &&
            $user->role?->role_name === 'Admin' &&
            ($role->role_name !== 'Admin' || !$validated['is_active'])
        ) {
            $activeAdmins = User::where('is_active', true)
                ->whereHas('role', function ($query) {
                    $query->where('role_name', 'Admin');
                })
                ->count();

            if ($activeAdmins <= 1) {
                return back()
                    ->withErrors([
                        'role_id' => 'Admin terakhir yang masih aktif tidak boleh diturunkan role atau dinonaktifkan.'
                    ])
                    ->withInput();
            }
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $role->id,
            'vendor_id' => $validated['vendor_id'] ?? null,
            'is_active' => $validated['is_active'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }
}
