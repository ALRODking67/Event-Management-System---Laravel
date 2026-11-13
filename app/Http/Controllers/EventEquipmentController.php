<?php

namespace App\Http\Controllers;

use App\Models\EventEquipment;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventEquipmentController extends Controller
{
    /**
     * Menampilkan penugasan peralatan milik organisasi.
     */
    public function index(Organization $organization)
    {
        $events = $organization->events()
            ->with([
                'equipmentAssignments' => function ($query) use ($organization) {
                    $query->whereHas('equipment', function ($equipmentQuery) use ($organization) {
                        $equipmentQuery->where(
                            'organization_id',
                            $organization->id
                        );
                    })->with('equipment.category');
                },
            ])
            ->orderBy('start_datetime')
            ->get();

        return view('event-equipment.index', compact(
            'events',
            'organization'
        ));
    }

    /**
     * Form tambah penugasan peralatan.
     */
    public function create(Organization $organization)
    {
        $events = $organization->events()
            ->orderBy('start_datetime')
            ->get();

        $equipment = $organization->equipment()
            ->with('category')
            ->where('availability_status', 'Available')
            ->orderBy('equipment_name')
            ->get();

        return view('event-equipment.create', compact(
            'events',
            'equipment',
            'organization'
        ));
    }

    /**
     * Menyimpan penugasan peralatan.
     */
    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                'integer',
                Rule::exists('events', 'id')
                    ->where('organization_id', $organization->id),
            ],

            'equipment_id' => [
                'required',
                'integer',
                Rule::exists('equipment', 'id')
                    ->where('organization_id', $organization->id),
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'usage_status' => [
                'required',
                Rule::in(['Planned', 'In Use', 'Returned']),
            ],

            'condition_notes' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['updated_by'] = auth()->id();

        EventEquipment::create($validated);

        return redirect()
            ->route('event-equipment.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Penugasan peralatan berhasil ditambahkan.');
    }

    /**
     * Form edit penugasan peralatan.
     */
    public function edit(Organization $organization, int $eventEquipment)
    {
        // Cari penugasan yang acara DAN peralatannya
        // sama-sama dimiliki organisasi aktif.
        $assignment = EventEquipment::query()
            ->whereHas('event', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->whereHas('equipment', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->with([
                'event',
                'equipment.category',
            ])
            ->findOrFail($eventEquipment);

        $events = $organization->events()
            ->orderBy('start_datetime')
            ->get();

        $equipment = $organization->equipment()
            ->with('category')
            ->orderBy('equipment_name')
            ->get();

        return view('event-equipment.edit', [
            'eventEquipment' => $assignment,
            'events' => $events,
            'equipment' => $equipment,
            'organization' => $organization,
        ]);
    }

    /**
     * Memperbarui penugasan peralatan.
     */
    public function update(
        Request $request,
        Organization $organization,
        int $eventEquipment
    ) {
        // Pastikan penugasan benar-benar milik organisasi aktif.
        $assignment = EventEquipment::query()
            ->whereHas('event', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->whereHas('equipment', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->findOrFail($eventEquipment);

        $validated = $request->validate([
            'event_id' => [
                'required',
                'integer',
                Rule::exists('events', 'id')
                    ->where('organization_id', $organization->id),
            ],

            'equipment_id' => [
                'required',
                'integer',
                Rule::exists('equipment', 'id')
                    ->where('organization_id', $organization->id),
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'usage_status' => [
                'required',
                Rule::in(['Planned', 'In Use', 'Returned']),
            ],

            'condition_notes' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['updated_by'] = auth()->id();

        $assignment->update($validated);

        return redirect()
            ->route('event-equipment.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Penugasan peralatan berhasil diperbarui.');
    }
}
