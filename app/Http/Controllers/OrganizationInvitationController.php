<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganizationInvitationController extends Controller
{
    /**
     * Membuat undangan untuk anggota organisasi.
     */
    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:Staf Operasional,Vendor'],
        ]);

        $role = Role::where('role_name', $validated['role'])->firstOrFail();

        $email = Str::lower($validated['email']);

        $existingUser = User::whereRaw('LOWER(email) = ?', [$email])
            ->first();

        // Periksa apakah pengguna sudah menjadi anggota organisasi.
        if (
            $existingUser &&
            $organization->members()
                ->where('user_id', $existingUser->id)
                ->exists()
        ) {
            return back()->withErrors([
                'email' => 'Pengguna tersebut sudah menjadi anggota organisasi ini.',
            ])->withInput();
        }

        $vendor = null;

        if ($validated['role'] === 'Vendor') {
            // Profil Vendor harus berasal dari organisasi yang sama.
            $vendor = Vendor::where('organization_id', $organization->id)
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            if (!$vendor) {
                return back()->withErrors([
                    'email' => 'Buat profil Vendor dengan email yang sama terlebih dahulu.',
                ])->withInput();
            }

            // Periksa apakah profil Vendor sudah terhubung
            // dengan anggota lain di organisasi ini.
            if (
                $organization->members()
                    ->where('vendor_id', $vendor->id)
                    ->exists()
            ) {
                return back()->withErrors([
                    'email' => 'Profil Vendor ini sudah terhubung dengan akun pengguna lain.',
                ])->withInput();
            }
        }

        // Cegah pembuatan undangan aktif berulang untuk email yang sama.
        $pendingInvitation = OrganizationInvitation::where(
            'organization_id',
            $organization->id
        )
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->exists();

        if ($pendingInvitation) {
            return back()->withErrors([
                'email' => 'Masih ada undangan aktif untuk email tersebut.',
            ])->withInput();
        }

        $plainToken = Str::random(64);

        OrganizationInvitation::create([
            'organization_id' => $organization->id,
            'email' => $email,
            'role_id' => $role->id,
            'invited_by' => $request->user()->id,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addHours(48),
        ]);

        $inviteUrl = route('invitations.accept', [
            'token' => $plainToken,
        ]);

        return back()
            ->with('inviteUrl', $inviteUrl)
            ->with('success', 'Undangan berhasil dibuat. Salin tautan sebelum meninggalkan halaman ini.');
    }

    /**
     * Menampilkan halaman penerimaan undangan.
     */
    public function show(string $token)
    {
        $invitation = OrganizationInvitation::with([
            'organization',
            'role',
        ])
            ->where('token_hash', hash('sha256', $token))
            ->first();

        if (
            !$invitation ||
            $invitation->accepted_at ||
            $invitation->revoked_at ||
            !$invitation->expires_at ||
            $invitation->expires_at->isPast()
        ) {
            abort(404, 'Undangan tidak ditemukan, sudah digunakan, atau kedaluwarsa.');
        }

        if (
            Str::lower(auth()->user()->email) !==
            Str::lower($invitation->email)
        ) {
            abort(403, 'Login menggunakan email yang menerima undangan ini.');
        }

        return view('organizations.invitation', compact(
            'invitation',
            'token'
        ));
    }

    /**
     * Memproses penerimaan undangan.
     */
    public function accept(Request $request, string $token)
    {
        $invitation = DB::transaction(function () use ($request, $token) {

            // Kunci baris undangan selama proses penerimaan.
            $invitation = OrganizationInvitation::where(
                'token_hash',
                hash('sha256', $token)
            )
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $invitation->accepted_at ||
                $invitation->revoked_at ||
                !$invitation->expires_at ||
                $invitation->expires_at->isPast()
            ) {
                abort(404, 'Undangan tidak berlaku lagi.');
            }

            if (
                Str::lower($request->user()->email) !==
                Str::lower($invitation->email)
            ) {
                abort(403, 'Email akun tidak cocok dengan email undangan.');
            }

            $organization = Organization::findOrFail(
                $invitation->organization_id
            );

            // Jangan menimpa keanggotaan atau peran yang sudah ada.
            $alreadyMember = $organization->members()
                ->where('user_id', $request->user()->id)
                ->exists();

            if ($alreadyMember) {
                abort(409, 'Anda sudah menjadi anggota organisasi ini.');
            }

            $vendorId = null;

            if ($invitation->role->role_name === 'Vendor') {

                // Cari dan kunci profil Vendor milik organisasi ini.
                $vendor = Vendor::where(
                    'organization_id',
                    $organization->id
                )
                    ->whereRaw(
                        'LOWER(email) = ?',
                        [Str::lower($invitation->email)]
                    )
                    ->lockForUpdate()
                    ->first();

                if (!$vendor) {
                    abort(404, 'Profil Vendor untuk undangan ini tidak ditemukan.');
                }

                // Periksa apakah Vendor sudah digunakan anggota lain.
                $vendorAlreadyLinked = $organization->members()
                    ->where('vendor_id', $vendor->id)
                    ->exists();

                if ($vendorAlreadyLinked) {
                    abort(409, 'Profil Vendor sudah terhubung dengan akun lain.');
                }

                $vendorId = $vendor->id;
            }

            // Hubungan Vendor disimpan pada organization_members,
            // bukan lagi pada users.vendor_id.
            $organization->members()->create([
                'user_id' => $request->user()->id,
                'role_id' => $invitation->role_id,
                'vendor_id' => $vendorId,
            ]);

            $invitation->accepted_at = now();
            $invitation->save();

            return $invitation;
        });

        return redirect()
            ->route('organizations.workspace', $invitation->organization->slug)
            ->with('success', 'Undangan berhasil diterima. Selamat bergabung!');
    }
}
