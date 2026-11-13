<?php

namespace App\Http\Controllers;

use App\Models\EventVendor;
use App\Models\Organization;
use App\Models\VendorConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorConfirmationController extends Controller
{
    /**
     * Mengambil role pengguna dalam organisasi aktif.
     */
    private function getOrganizationRole(
        Request $request,
        Organization $organization
    ): ?string {
        $membership = $request->user()
            ->organizationMemberships()
            ->with('role')
            ->where('organization_id', $organization->id)
            ->first();

        return $membership?->role?->role_name;
    }

    /**
     * Mengambil vendor_id dari keanggotaan organisasi,
     * bukan dari users.vendor_id.
     */
    private function ensureVendorLinked(
        Request $request,
        Organization $organization
    ): int {
        $membership = $request->user()
            ->organizationMemberships()
            ->where('organization_id', $organization->id)
            ->first();

        if (!$membership || !$membership->vendor_id) {
            abort(403, 'Akun Vendor belum terhubung dengan data vendor organisasi ini.');
        }

        return (int) $membership->vendor_id;
    }

    /**
     * Menampilkan pusat konfirmasi dan negosiasi vendor.
     */
    public function index(Request $request, Organization $organization)
    {
        $role = $this->getOrganizationRole($request, $organization);

        abort_unless(
            in_array($role, ['Admin', 'Staf Operasional', 'Vendor'], true),
            403,
            'Anda tidak memiliki akses ke halaman ini.'
        );

        $query = $organization->eventVendors()
            ->with([
                'event',
                'vendor',
                'offers',
                'confirmations' => function ($query) {
                    $query->orderByDesc('confirmed_at')
                        ->orderByDesc('id');
                },
            ]);

        if ($role === 'Vendor') {
            $vendorId = $this->ensureVendorLinked($request, $organization);

            $query->where('vendor_id', $vendorId);
        }

        $eventVendors = $query
            ->orderByDesc('created_at')
            ->get();

        /*
         * Mengambil user yang menjadi anggota vendor
         * dalam organisasi aktif.
         *
         * Ini digunakan untuk mengetahui apakah penawaran
         * terakhir dikirim oleh Vendor atau Admin/Staf.
         */
        $vendorMemberUserIds = DB::table('organization_members')
            ->where('organization_id', $organization->id)
            ->whereNotNull('vendor_id')
            ->get(['vendor_id', 'user_id'])
            ->groupBy('vendor_id')
            ->map(function ($members) {
                return $members->pluck('user_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();
            })
            ->all();

        return view(
            'vendor-confirmations.index',
            compact(
                'organization',
                'eventVendors',
                'role',
                'vendorMemberUserIds'
            )
        );
    }

    /**
     * Form jawaban ketersediaan Vendor.
     */
    public function create(Request $request, Organization $organization)
    {
        $role = $this->getOrganizationRole($request, $organization);

        if ($role !== 'Vendor') {
            abort(403, 'Hanya Vendor yang dapat mengirim jawaban ketersediaan.');
        }

        $vendorId = $this->ensureVendorLinked($request, $organization);

        $eventVendors = $organization->eventVendors()
            ->with(['event', 'vendor'])
            ->where('vendor_id', $vendorId)
            ->where('assignment_status', 'Requested')
            ->orderByDesc('created_at')
            ->get();

        return view(
            'vendor-confirmations.create',
            compact('organization', 'eventVendors')
        );
    }

    /**
     * Menyimpan jawaban ketersediaan Vendor.
     */
    public function store(Request $request, Organization $organization)
    {
        $role = $this->getOrganizationRole($request, $organization);

        if ($role !== 'Vendor') {
            abort(403, 'Hanya Vendor yang dapat mengirim jawaban ketersediaan.');
        }

        $vendorId = $this->ensureVendorLinked($request, $organization);

        $validated = $request->validate([
            'event_vendor_id' => [
                'required',
                'integer',
                'exists:event_vendors,id',
            ],
            'availability_status' => [
                'required',
                'in:Available,Unavailable',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $eventVendor = $organization->eventVendors()
            ->where('vendor_id', $vendorId)
            ->where('assignment_status', 'Requested')
            ->findOrFail($validated['event_vendor_id']);

        VendorConfirmation::create([
            'event_vendor_id' => $eventVendor->id,
            'availability_status' => $validated['availability_status'],
            'confirmed_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('vendor-confirmations.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Jawaban ketersediaan berhasil disimpan.');
    }
}