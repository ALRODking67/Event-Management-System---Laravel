<?php

namespace App\Http\Controllers;

use App\Models\EventVendor;
use App\Models\VendorConfirmation;
use Illuminate\Http\Request;

class VendorConfirmationController extends Controller
{
    /**
     * Memastikan pengguna memiliki role Vendor.
     */
    private function ensureVendorRole(Request $request): void
    {
        if ($request->user()->role?->role_name !== 'Vendor') {
            abort(403, 'Hanya akun Vendor yang dapat mengirim jawaban ketersediaan.');
        }
    }

    /**
     * Memastikan akun Vendor sudah terhubung dengan data vendor.
     */
    private function ensureVendorLinked(Request $request): void
    {
        $user = $request->user();

        if (
            $user->role?->role_name === 'Vendor'
            && !$user->vendor_id
        ) {
            abort(403, 'Akun Vendor belum terhubung dengan data vendor.');
        }
    }

    /**
     * Memastikan penugasan merupakan milik Vendor yang sedang login.
     */
    private function ensureEventVendorAccess(
        Request $request,
        EventVendor $eventVendor
    ): void {
        $user = $request->user();

        if (
            (int) $eventVendor->vendor_id !== (int) $user->vendor_id
        ) {
            abort(403, 'Anda tidak memiliki akses ke penugasan vendor ini.');
        }
    }

    /**
     * Menampilkan daftar konfirmasi.
     */
    public function index(Request $request)
    {
        $this->ensureVendorLinked($request);

        $user = $request->user();

        $eventVendors = EventVendor::with([
            'event',
            'vendor',
            'confirmations' => function ($query) {
                $query->orderByDesc('confirmed_at')
                    ->orderByDesc('id');
            },
        ])
            ->when(
                $user->role?->role_name === 'Vendor',
                function ($query) use ($user) {
                    $query->where('vendor_id', $user->vendor_id);
                }
            )
            ->orderByDesc('created_at')
            ->get();

        return view(
            'vendor-confirmations.index',
            compact('eventVendors')
        );
    }

    /**
     * Menampilkan form jawaban ketersediaan.
     */
    public function create(Request $request)
    {
        $this->ensureVendorRole($request);
        $this->ensureVendorLinked($request);

        $user = $request->user();

        $eventVendors = EventVendor::with([
            'event',
            'vendor',
        ])
            ->where('vendor_id', $user->vendor_id)
            ->where('assignment_status', 'Requested')
            ->orderByDesc('created_at')
            ->get();

        return view(
            'vendor-confirmations.create',
            compact('eventVendors')
        );
    }

    /**
     * Menyimpan jawaban baru sebagai riwayat.
     */
    public function store(Request $request)
    {
        $this->ensureVendorRole($request);
        $this->ensureVendorLinked($request);

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

        $eventVendor = EventVendor::findOrFail(
            $validated['event_vendor_id']
        );

        $this->ensureEventVendorAccess($request, $eventVendor);

        // Vendor hanya boleh menjawab penugasan yang masih Requested.
        if ($eventVendor->assignment_status !== 'Requested') {
            abort(
                403,
                'Penugasan ini sudah diproses dan tidak menerima jawaban ketersediaan baru.'
            );
        }

        VendorConfirmation::create([
            'event_vendor_id' => $eventVendor->id,
            'availability_status' => $validated['availability_status'],
            'confirmed_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('vendor-confirmations.index')
            ->with(
                'success',
                'Jawaban ketersediaan berhasil disimpan.'
            );
    }

    /**
     * Riwayat konfirmasi tidak dapat diedit.
     * Jawaban baru harus disimpan sebagai catatan baru.
     */
    public function edit(
        Request $request,
        VendorConfirmation $vendorConfirmation
    ) {
        abort(
            403,
            'Riwayat konfirmasi tidak dapat diedit. Jawaban baru harus dibuat sebagai catatan terpisah.'
        );
    }

    /**
     * Riwayat konfirmasi tidak dapat ditimpa.
     */
    public function update(
        Request $request,
        VendorConfirmation $vendorConfirmation
    ) {
        abort(
            403,
            'Riwayat konfirmasi tidak dapat diperbarui agar catatan jawaban tetap terjaga.'
        );
    }
}
