<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventEquipment;
use App\Models\Equipment;
use Illuminate\Http\Request;

class EventEquipmentController extends Controller
{
    public function index()
    {
        $events = Event::with([
            'equipmentAssignments.equipment.category'
        ])
            ->orderBy('start_datetime')
            ->get();

        return view('event-equipment.index', compact('events'));
    }

    public function create()
    {
        $events = Event::orderBy('start_datetime')->get();

        $equipment = Equipment::with('category')
            ->where('availability_status', 'Available')
            ->orderBy('equipment_name')
            ->get();

        return view('event-equipment.create', compact('events', 'equipment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                'exists:events,id',
            ],
            'equipment_id' => [
                'required',
                'exists:equipment,id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'usage_status' => [
                'required',
                'in:Planned,In Use,Returned',
            ],
            'condition_notes' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['updated_by'] = auth()->id();

        EventEquipment::create($validated);

        return redirect()
            ->route('event-equipment.index')
            ->with('success', 'Penugasan peralatan berhasil ditambahkan.');
    }

    public function edit(EventEquipment $eventEquipment)
    {
        $events = Event::orderBy('start_datetime')->get();

        $equipment = Equipment::with('category')
            ->orderBy('equipment_name')
            ->get();

        return view('event-equipment.edit', compact(
            'eventEquipment',
            'events',
            'equipment'
        ));
    }

    public function update(Request $request, EventEquipment $eventEquipment)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                'exists:events,id',
            ],
            'equipment_id' => [
                'required',
                'exists:equipment,id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'usage_status' => [
                'required',
                'in:Planned,In Use,Returned',
            ],
            'condition_notes' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['updated_by'] = auth()->id();

        $eventEquipment->update($validated);

        return redirect()
            ->route('event-equipment.index')
            ->with('success', 'Penugasan peralatan berhasil diperbarui.');
    }
}