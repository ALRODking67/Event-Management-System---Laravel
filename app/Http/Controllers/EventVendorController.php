<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventVendor;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EventVendorController extends Controller
{
    public function index()
    {
        $events = Event::with([
            'vendorAssignments.vendor',
            'vendorAssignments.confirmations' => function ($query) {
                $query->orderByDesc('confirmed_at')
                    ->orderByDesc('id');
            },
        ])
            ->orderBy('start_datetime')
            ->get();

        return view('event-vendors.index', compact('events'));
    }

    public function create()
    {
        $events = Event::orderBy('start_datetime')->get();
        $vendors = Vendor::orderBy('vendor_name')->get();

        return view('event-vendors.create', compact('events', 'vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                'exists:events,id',
            ],
            'vendor_id' => [
                'required',
                'exists:vendors,id',
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

        // Setiap penugasan baru selalu menunggu jawaban Vendor.
        $validated['assignment_status'] = 'Requested';

        EventVendor::create($validated);

        return redirect()
            ->route('event-vendors.index')
            ->with('success', 'Penugasan vendor berhasil ditambahkan.');
    }

    public function edit(EventVendor $eventVendor)
    {
        $eventVendor->load([
            'event',
            'vendor',
            'confirmations' => function ($query) {
                $query->orderByDesc('confirmed_at')
                    ->orderByDesc('id');
            },
        ]);

        $events = Event::orderBy('start_datetime')->get();
        $vendors = Vendor::orderBy('vendor_name')->get();

        return view('event-vendors.edit', compact(
            'eventVendor',
            'events',
            'vendors'
        ));
    }

    public function update(Request $request, EventVendor $eventVendor)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                'exists:events,id',
            ],
            'vendor_id' => [
                'required',
                'exists:vendors,id',
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

        $latestConfirmation = $eventVendor->confirmations()
            ->orderByDesc('confirmed_at')
            ->orderByDesc('id')
            ->first();

        $newStatus = $validated['assignment_status'];

        // Penugasan yang sudah dijawab tidak boleh dikembalikan
        // menjadi Requested melalui form edit.
        if (
            $latestConfirmation &&
            $newStatus === 'Requested'
        ) {
            throw ValidationException::withMessages([
                'assignment_status' => 'Penugasan yang sudah memiliki jawaban Vendor tidak dapat dikembalikan ke Requested.',
            ]);
        }

        // Vendor harus menjawab tersedia sebelum penugasan disetujui.
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

        // Pastikan urutan tahapan pekerjaan tidak dilompati.
        if (
            $newStatus === 'In Progress' &&
            $eventVendor->assignment_status !== 'Confirmed' &&
            $eventVendor->assignment_status !== 'In Progress'
        ) {
            throw ValidationException::withMessages([
                'assignment_status' => 'Penugasan harus berstatus Confirmed sebelum pekerjaan dimulai.',
            ]);
        }

        if (
            $newStatus === 'Completed' &&
            $eventVendor->assignment_status !== 'In Progress' &&
            $eventVendor->assignment_status !== 'Completed'
        ) {
            throw ValidationException::withMessages([
                'assignment_status' => 'Penugasan harus berstatus In Progress sebelum dinyatakan selesai.',
            ]);
        }

        $eventVendor->update($validated);

        return redirect()
            ->route('event-vendors.index')
            ->with('success', 'Penugasan vendor berhasil diperbarui.');
    }
}
