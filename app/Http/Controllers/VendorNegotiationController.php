<?php

namespace App\Http\Controllers;

use App\Models\EventVendor;
use App\Models\EventVendorOffer;
use App\Models\Organization;
use App\Models\EventVendorDecisionRequest;
use App\Models\OrganizationMember;
use App\Notifications\EventVendorDecisionNotification;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorNegotiationController extends Controller
{
    /**
     * Mengambil keanggotaan user pada organisasi aktif.
     */
    private function getMembership(Organization $organization)
    {
        $membership = auth()->user()
            ->organizationMemberships()
            ->with('role')
            ->where('organization_id', $organization->id)
            ->first();

        abort_unless($membership, 403, 'Anda bukan anggota organisasi ini.');

        return $membership;
    }

    /**
     * Mengambil nama role dari relasi role.
     */
    private function getRoleName($membership): string
    {
        $role = $membership->role;

        return (string) (
            $role?->getAttribute('name')
            ?? $role?->getAttribute('role_name')
            ?? ''
        );
    }

    /**
     * Memastikan user adalah Admin atau Staf Operasional.
     */
    private function ensureStaff($membership): void
    {
        abort_unless(
            in_array($this->getRoleName($membership), ['Admin', 'Staf Operasional'], true),
            403,
            'Hanya Admin dan Staf Operasional yang dapat melakukan tindakan ini.'
        );
    }

    /**
     * Memastikan EventVendor berada di organisasi aktif
     * dan, jika user adalah Vendor, merupakan vendor miliknya.
     */
    private function ensureCanAccess(
        Organization $organization,
        EventVendor $eventVendor,
        $membership
    ): void {
        abort_unless(
            $eventVendor->event
                && (int) $eventVendor->event->organization_id === (int) $organization->id,
            404
        );

        $roleName = $this->getRoleName($membership);

        if ($roleName === 'Vendor') {
            abort_unless(
                $membership->vendor_id
                    && (int) $membership->vendor_id === (int) $eventVendor->vendor_id,
                403,
                'Anda tidak memiliki akses ke negosiasi vendor ini.'
            );

            return;
        }

        $this->ensureStaff($membership);
    }

    /**
     * Daftar negosiasi vendor.
     */
    public function index(Organization $organization)
    {
        $membership = $this->getMembership($organization);
        $roleName = $this->getRoleName($membership);

        $query = EventVendor::with([
            'event',
            'vendor',
            'offers.offeredBy',
        ])->whereHas('event', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        });

        if ($roleName === 'Vendor') {
            abort_unless($membership->vendor_id, 403, 'Akun Vendor belum terhubung dengan data vendor.');

            $query->where('vendor_id', $membership->vendor_id);
        } else {
            $this->ensureStaff($membership);
        }

        $assignments = $query->reorder()->latest()->get();

        return view('vendor-negotiations.index', compact(
            'organization',
            'assignments',
            'roleName'
        ));
    }

    /**
     * Detail negosiasi dan riwayat penawaran.
     */
    public function show(
        Organization $organization,
        EventVendor $eventVendor
    ) {
        $membership = $this->getMembership($organization);

        $this->ensureCanAccess($organization, $eventVendor, $membership);

        $eventVendor->load([
            'event',
            'vendor',
            'offers.offeredBy',
            'offers.respondedBy',
            'offers.acceptedBy',
            'payments',
            'decisionRequests.requester',
            'decisionRequests.reviewer',
            'decisionRequests.offer',
        ]);

        $roleName = $this->getRoleName($membership);

        return view('vendor-negotiations.show', compact(
            'organization',
            'eventVendor',
            'roleName'
        ));
    }

    
    /**
     * Mengirim counteroffer atau penawaran baru setelah penolakan.
     */
    public function counter(
        Request $request,
        Organization $organization,
        EventVendor $eventVendor
    ) {
        $membership = $this->getMembership($organization);

        $this->ensureCanAccess($organization, $eventVendor, $membership);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'start_datetime' => ['nullable', 'date'],
            'end_datetime' => ['nullable', 'date', 'after_or_equal:start_datetime'],
            'scope_description' => ['required', 'string', 'max:10000'],
            'payment_terms' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($eventVendor, $validated) {
            $lockedEventVendor = EventVendor::whereKey($eventVendor->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Kesepakatan yang sudah diterima tidak boleh dinegosiasikan ulang.
            abort_if(
                $lockedEventVendor->agreement_accepted_at !== null,
                422,
                'Kesepakatan sudah disetujui dan tidak dapat dinegosiasikan ulang.'
            );

            $latestOffer = $lockedEventVendor->offers()
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            abort_unless(
                $latestOffer,
                422,
                'Belum ada penawaran awal untuk ditanggapi.'
            );

            if ($latestOffer->status === 'Pending') {
                abort_if(
                    (int) $latestOffer->offered_by_user_id === (int) auth()->id(),
                    403,
                    'Anda tidak dapat membalas penawaran yang Anda kirim sendiri.'
                );

                // Penawaran sebelumnya tetap tersimpan sebagai riwayat.
                $latestOffer->update([
                    'status' => 'Superseded',
                    'responded_by_user_id' => auth()->id(),
                    'responded_at' => now(),
                ]);
            } elseif ($latestOffer->status !== 'Rejected') {
                abort(
                    422,
                    'Penawaran terakhir tidak dapat dilanjutkan. Periksa status negosiasi.'
                );
            }

            // Membuat penawaran baru tanpa menghapus riwayat sebelumnya.
            $lockedEventVendor->offers()->create([
                'offered_by_user_id' => auth()->id(),
                'offer_type' => 'counter',
                'amount' => $validated['amount'],
                'start_datetime' => $validated['start_datetime'] ?? null,
                'end_datetime' => $validated['end_datetime'] ?? null,
                'scope_description' => $validated['scope_description'],
                'payment_terms' => $validated['payment_terms'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'Pending',
            ]);

            $lockedEventVendor->update([
                'agreement_status' => 'negotiating',
            ]);
        });

        return redirect()
            ->route('vendor-negotiations.show', [
                'organization' => $organization->slug,
                'eventVendor' => $eventVendor->id,
            ])
            ->with('success', 'Penawaran baru berhasil dikirim.');
    }


    
    /**
     * Menerima atau menolak penawaran dari pihak lain.
     */
    public function respond(
        Request $request,
        Organization $organization,
        EventVendor $eventVendor,
        EventVendorOffer $offer
    ) {
        $membership = $this->getMembership($organization);

        $this->ensureCanAccess($organization, $eventVendor, $membership);

        abort_unless(
            (int) $offer->event_vendor_id === (int) $eventVendor->id,
            404
        );

        abort_if(
            (int) $offer->offered_by_user_id === (int) auth()->id(),
            403,
            'Anda tidak dapat merespons penawaran yang Anda kirim sendiri.'
        );

        $validated = $request->validate([
            'action' => ['required', 'in:accept,reject'],
            'response_note' => [
                'nullable',
                'string',
                'max:5000',
                'required_if:action,reject',
            ],
        ], [
            'response_note.required_if' => 'Alasan penolakan wajib diisi.',
        ]);

        DB::transaction(function () use ($eventVendor, $offer, $validated) {
            $lockedEventVendor = EventVendor::whereKey($eventVendor->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(
                $lockedEventVendor->agreement_accepted_at !== null,
                422,
                'Kesepakatan sudah disetujui.'
            );

            $lockedOffer = EventVendorOffer::whereKey($offer->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                (int) $lockedOffer->event_vendor_id === (int) $lockedEventVendor->id
                && $lockedOffer->status === 'Pending',
                422,
                'Penawaran ini sudah tidak aktif.'
            );

            $latestOfferId = $lockedEventVendor->offers()
                ->reorder()->latest('id')
                ->value('id');

            abort_unless(
                (int) $latestOfferId === (int) $lockedOffer->id,
                422,
                'Sudah ada penawaran yang lebih baru.'
            );

            $accepted = $validated['action'] === 'accept';

            $lockedOffer->update([
                'status' => $accepted ? 'Accepted' : 'Rejected',
                'responded_by_user_id' => auth()->id(),
                'responded_at' => now(),
                'response_note' => $accepted
                    ? null
                    : $validated['response_note'],
                'accepted_by_user_id' => $accepted
                    ? auth()->id()
                    : null,
                'accepted_at' => $accepted ? now() : null,
            ]);

            if ($accepted) {
                $lockedEventVendor->update([
                    'agreed_cost' => $lockedOffer->amount,
                    'agreed_start_datetime' => $lockedOffer->start_datetime,
                    'agreed_end_datetime' => $lockedOffer->end_datetime,
                    'agreed_scope' => $lockedOffer->scope_description,
                    'agreed_payment_terms' => $lockedOffer->payment_terms,
                    'agreement_status' => 'agreed',
                    'agreement_accepted_at' => now(),
                    'agreement_accepted_by' => auth()->id(),
                    'assignment_status' => 'Confirmed',
                ]);
            }
        });

        $message = $validated['action'] === 'accept'
            ? 'Penawaran berhasil diterima. Kesepakatan telah tercatat.'
            : 'Penawaran berhasil ditolak. Alasan penolakan telah disimpan.';

        return redirect()
            ->route('vendor-negotiations.show', [
                'organization' => $organization->slug,
                'eventVendor' => $eventVendor->id,
            ])
            ->with('success', $message);
    }


    /**
     * Vendor mengajukan permintaan untuk menerima kembali penawaran yang pernah ditolak.
     */
    public function requestDecision(
        Organization $organization,
        EventVendor $eventVendor
    ) {
        $membership = $this->getMembership($organization);

        $this->ensureCanAccess($organization, $eventVendor, $membership);

        abort_unless(
            $this->getRoleName($membership) === 'Vendor',
            403,
            'Hanya Vendor yang dapat mengajukan perubahan keputusan.'
        );

        abort_if(
            $eventVendor->agreement_accepted_at !== null,
            422,
            'Kesepakatan sudah disetujui.'
        );

        $latestOffer = $eventVendor->offers()
            ->reorder()->latest('id')
            ->first();

        abort_unless(
            $latestOffer && $latestOffer->status === 'Rejected',
            422,
            'Hanya penawaran yang sudah ditolak yang dapat diajukan kembali.'
        );

        $hasPendingRequest = EventVendorDecisionRequest::where(
            'event_vendor_id',
            $eventVendor->id
        )->where('status', 'Pending')->exists();

        abort_if(
            $hasPendingRequest,
            422,
            'Masih ada permintaan perubahan keputusan yang menunggu persetujuan Admin.'
        );

        $decisionRequest = DB::transaction(function () use ($eventVendor, $latestOffer) {
            return EventVendorDecisionRequest::create([
                'event_vendor_id' => $eventVendor->id,
                'event_vendor_offer_id' => $latestOffer->id,
                'requested_by_user_id' => auth()->id(),
                'requested_decision' => 'accept',
                'status' => 'Pending',
            ]);
        });

        $eventVendor->load('event', 'vendor');

        $adminMembers = OrganizationMember::with('user', 'role')
            ->where('organization_id', $organization->id)
            ->whereHas('role', function ($query) {
                $query->whereIn('role_name', ['Admin', 'Staf Operasional']);
            })
            ->get();

        $url = route('vendor-negotiations.show', [
            'organization' => $organization->slug,
            'eventVendor' => $eventVendor->id,
        ]);

        foreach ($adminMembers as $member) {
            if ($member->user) {
                $member->user->notify(new EventVendorDecisionNotification(
                    'Permintaan Perubahan Keputusan Vendor',
                    auth()->user()->name . ' meminta persetujuan untuk menerima penawaran pada acara ' .
                        ($eventVendor->event->event_name ?? '-'),
                    $url
                ));
            }
        }

        return redirect()->route('vendor-negotiations.show', [
            'organization' => $organization->slug,
            'eventVendor' => $eventVendor->id,
        ])->with('success', 'Permintaan perubahan keputusan berhasil dikirim. Menunggu persetujuan Admin.');
    }


    /**
     * Admin atau Staf Operasional meninjau permintaan perubahan keputusan Vendor.
     */
    public function reviewDecision(
        Request $request,
        Organization $organization,
        EventVendor $eventVendor,
        EventVendorDecisionRequest $decisionRequest
    ) {
        $membership = $this->getMembership($organization);

        $this->ensureStaff($membership);

        abort_unless(
            (int) $decisionRequest->event_vendor_id === (int) $eventVendor->id,
            404
        );

        abort_unless(
            $eventVendor->event &&
            (int) $eventVendor->event->organization_id === (int) $organization->id,
            404
        );

        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'admin_note' => [
                'nullable',
                'string',
                'max:5000',
                'required_if:action,reject',
            ],
        ], [
            'admin_note.required_if' => 'Catatan wajib diisi jika permintaan ditolak.',
        ]);

        $requester = null;

        DB::transaction(function () use (
            $eventVendor,
            $decisionRequest,
            $validated,
            &$requester
        ) {
            $lockedRequest = EventVendorDecisionRequest::whereKey($decisionRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $lockedRequest->status === 'Pending',
                422,
                'Permintaan ini sudah pernah ditinjau.'
            );

            $lockedEventVendor = EventVendor::whereKey($eventVendor->id)
                ->lockForUpdate()
                ->firstOrFail();

            $offer = EventVendorOffer::whereKey($lockedRequest->event_vendor_offer_id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                (int) $offer->event_vendor_id === (int) $lockedEventVendor->id &&
                $offer->status === 'Rejected',
                422,
                'Penawaran tidak lagi tersedia untuk disetujui.'
            );

            if ($validated['action'] === 'approve') {
                abort_if(
                    $lockedEventVendor->agreement_accepted_at !== null,
                    422,
                    'Penugasan ini sudah memiliki kesepakatan.'
                );

                $requester = \App\Models\User::findOrFail(
                    $lockedRequest->requested_by_user_id
                );

                $offer->update([
                    'status' => 'Accepted',
                    'responded_by_user_id' => auth()->id(),
                    'responded_at' => now(),
                    'accepted_by_user_id' => $lockedRequest->requested_by_user_id,
                    'accepted_at' => now(),
                ]);

                $lockedEventVendor->update([
                    'agreed_cost' => $offer->amount,
                    'agreed_start_datetime' => $offer->start_datetime,
                    'agreed_end_datetime' => $offer->end_datetime,
                    'agreed_scope' => $offer->scope_description,
                    'agreed_payment_terms' => $offer->payment_terms,
                    'agreement_status' => 'agreed',
                    'agreement_accepted_at' => now(),
                    'agreement_accepted_by' => auth()->id(),
                    'assignment_status' => 'Confirmed',
                ]);
            } else {
                $requester = \App\Models\User::findOrFail(
                    $lockedRequest->requested_by_user_id
                );
            }

            $lockedRequest->update([
                'status' => $validated['action'] === 'approve'
                    ? 'Approved'
                    : 'Rejected',
                'reviewed_by_user_id' => auth()->id(),
                'reviewed_at' => now(),
                'admin_note' => $validated['admin_note'] ?? null,
            ]);
        });

        if ($requester) {
            $requester->notify(new EventVendorDecisionNotification(
                $validated['action'] === 'approve'
                    ? 'Permintaan Perubahan Keputusan Disetujui'
                    : 'Permintaan Perubahan Keputusan Ditolak',
                $validated['action'] === 'approve'
                    ? 'Admin menyetujui permintaan Anda. Penawaran kini tercatat sebagai kesepakatan.'
                    : 'Admin menolak permintaan Anda. Catatan: ' . $validated['admin_note'],
                route('vendor-negotiations.show', [
                    'organization' => $organization->slug,
                    'eventVendor' => $eventVendor->id,
                ])
            ));
        }

        return redirect()->route('vendor-negotiations.show', [
            'organization' => $organization->slug,
            'eventVendor' => $eventVendor->id,
        ])->with(
            'success',
            $validated['action'] === 'approve'
                ? 'Permintaan Vendor disetujui. Kesepakatan berhasil dicatat.'
                : 'Permintaan Vendor ditolak dan catatan keputusan berhasil disimpan.'
        );
    }
}