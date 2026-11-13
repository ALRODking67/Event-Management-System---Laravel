<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventVendor;
use App\Models\Organization;
use App\Models\Vendor;
use App\Models\EventVendorOffer;
use App\Models\OrganizationMember;
use App\Notifications\EventVendorDecisionNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EventVendorController extends Controller
{
    public function index(Organization $organization)
    {
        // Halaman utama penugasan vendor menggunakan
        // halaman Vendor Confirmation yang sudah terintegrasi.
        return redirect()->route('vendor-confirmations.index', [
            'organization' => $organization->slug,
        ]);
    }

    public function create(Organization $organization)
    {
        $events = $organization->events()
            ->orderBy('start_datetime')
            ->get();

        $vendors = $organization->vendors()
            ->orderBy('vendor_name')
            ->get();

        return view('event-vendors.create', compact(
            'events',
            'vendors',
            'organization'
        ));
    }

    
    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                Rule::exists('events', 'id')
                    ->where('organization_id', $organization->id),
            ],
            'vendor_id' => [
                'required',
                Rule::exists('vendors', 'id')
                    ->where('organization_id', $organization->id),
            ],
            'service_description' => [
                'required',
                'string',
            ],
            'agreed_cost' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        // Buat penugasan dan penawaran awal dalam satu transaksi.
        $eventVendor = DB::transaction(function () use ($validated) {

            $eventVendor = EventVendor::create([
                'event_id' => $validated['event_id'],
                'vendor_id' => $validated['vendor_id'],
                'service_description' => $validated['service_description'],
                'agreed_cost' => $validated['agreed_cost'],
                'assignment_status' => 'Requested',
            ]);

            EventVendorOffer::create([
                'event_vendor_id' => $eventVendor->id,
                'offered_by_user_id' => auth()->id(),
                'offer_type' => 'initial',
                'amount' => $validated['agreed_cost'],
                'scope_description' => $validated['service_description'],
                'status' => 'Pending',
            ]);

            return $eventVendor;
        });

        // Ambil informasi acara untuk isi notifikasi.
        $eventVendor->load(['event', 'vendor']);

        // Cari akun Vendor yang terhubung dengan Vendor tujuan
        // di organisasi aktif.
        $vendorMembers = OrganizationMember::with('user', 'role')
            ->where('organization_id', $organization->id)
            ->where('vendor_id', $eventVendor->vendor_id)
            ->whereHas('role', function ($query) {
                $query->where('role_name', 'Vendor');
            })
            ->get();

        $url = route('vendor-negotiations.show', [
            'organization' => $organization->slug,
            'eventVendor' => $eventVendor->id,
        ]);

        // Kirim notifikasi kepada seluruh akun Vendor terkait.
        foreach ($vendorMembers as $member) {
            if ($member->user) {
                $member->user->notify(
                    new EventVendorDecisionNotification(
                        'Permintaan Pekerjaan Baru',
                        'Anda menerima permintaan pekerjaan untuk acara "' .
                            ($eventVendor->event->event_name ?? '-') .
                            '". Silakan periksa detail dan berikan tanggapan.',
                        $url,
                        (int) $organization->id,
                        'vendor_negotiation'
                    )
                );
            }
        }

        return redirect()
            ->route('vendor-confirmations.index', [
                'organization' => $organization->slug,
            ])
            ->with(
                'success',
                'Penugasan berhasil dibuat dan notifikasi telah dikirim kepada Vendor.'
            );
    }


    public function edit(
        Organization $organization,
        EventVendor $eventVendor
    ) {
        $eventVendor->load([
            'event',
            'vendor',
            'confirmations' => function ($query) {
                $query->orderByDesc('confirmed_at')
                    ->orderByDesc('id');
            },
        ]);

        $events = $organization->events()
            ->orderBy('start_datetime')
            ->get();

        $vendors = $organization->vendors()
            ->orderBy('vendor_name')
            ->get();

        return view('event-vendors.edit', compact(
            'eventVendor',
            'events',
            'vendors',
            'organization'
        ));
    }

    public function update(
        Request $request,
        Organization $organization,
        EventVendor $eventVendor
    ) {
        $validated = $request->validate([
            'event_id' => [
                'required',
                Rule::exists('events', 'id')
                    ->where('organization_id', $organization->id),
            ],
            'vendor_id' => [
                'required',
                Rule::exists('vendors', 'id')
                    ->where('organization_id', $organization->id),
            ],
            'service_description' => [
                'required',
                'string',
            ],
            'agreed_cost' => [
                'required',
                'numeric',
                'min:0',
            ],
            'assignment_status' => [
                'required',
                Rule::in([
                    'Requested',
                    'Confirmed',
                    'In Progress',
                    'Completed',
                ]),
            ],
        ]);

        $latestConfirmation = $eventVendor->confirmations()
            ->orderByDesc('confirmed_at')
            ->orderByDesc('id')
            ->first();

        $hasConfirmations = $eventVendor->confirmations()->exists();

        // Penugasan yang memiliki riwayat konfirmasi tidak boleh
        // dipindahkan ke acara atau Vendor lain.
        if (
            $hasConfirmations &&
            (
                (int) $validated['event_id'] !== (int) $eventVendor->event_id ||
                (int) $validated['vendor_id'] !== (int) $eventVendor->vendor_id
            )
        ) {
            throw ValidationException::withMessages([
                'vendor_id' => 'Acara atau Vendor tidak dapat diganti karena penugasan ini sudah memiliki riwayat konfirmasi. Buat penugasan baru untuk Vendor pengganti.',
            ]);
        }

        $newStatus = $validated['assignment_status'];

        // Penugasan yang sudah dijawab tidak boleh dikembalikan
        // menjadi Requested.
        if ($latestConfirmation && $newStatus === 'Requested') {
            throw ValidationException::withMessages([
                'assignment_status' => 'Penugasan yang sudah memiliki jawaban Vendor tidak dapat dikembalikan ke Requested.',
            ]);
        }

        // Vendor harus menjawab Available sebelum penugasan disetujui.
        if (
            in_array($newStatus, ['Confirmed', 'In Progress', 'Completed'], true) &&
            (
                !$latestConfirmation ||
                $latestConfirmation->availability_status !== 'Available'
            )
        ) {
            throw ValidationException::withMessages([
                'assignment_status' => 'Vendor harus memberikan jawaban Tersedia terlebih dahulu.',
            ]);
        }

        // Tahapan pekerjaan harus berurutan.
        if (
            $newStatus === 'In Progress' &&
            !in_array($eventVendor->assignment_status, ['Confirmed', 'In Progress'], true)
        ) {
            throw ValidationException::withMessages([
                'assignment_status' => 'Penugasan harus berstatus Confirmed sebelum pekerjaan dimulai.',
            ]);
        }

        if (
            $newStatus === 'Completed' &&
            !in_array($eventVendor->assignment_status, ['In Progress', 'Completed'], true)
        ) {
            throw ValidationException::withMessages([
                'assignment_status' => 'Penugasan harus berstatus In Progress sebelum dinyatakan selesai.',
            ]);
        }

        $eventVendor->update($validated);

        return redirect()
            ->route('vendor-confirmations.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Penugasan vendor berhasil diperbarui.');
    }
}
