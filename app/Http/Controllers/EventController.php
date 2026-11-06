<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index(Organization $organization)
    {
        $events = $organization->events()
            ->with('venue')
            ->orderBy('start_datetime')
            ->get();

        return view('events.index', compact('events', 'organization'));
    }

    public function create(Organization $organization)
    {
        $venues = $organization->venues()
            ->orderBy('venue_name')
            ->get();

        return view('events.create', compact('venues', 'organization'));
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'event_name' => ['required', 'string', 'max:150'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => [
                'required',
                'date',
                'after:start_datetime',
            ],
            'status' => [
                'required',
                'string',
                Rule::in(['Planning', 'Ongoing', 'Completed', 'Cancelled']),
            ],
            'venue_id' => [
                'nullable',
                Rule::exists('venues', 'id')
                    ->where('organization_id', $organization->id),
            ],
        ]);

        $validated['created_by'] = auth()->id();

        $organization->events()->create($validated);

        return redirect()
            ->route('events.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Acara berhasil dibuat.');
    }

    public function edit(Organization $organization, Event $event)
    {
        $venues = $organization->venues()
            ->orderBy('venue_name')
            ->get();

        return view('events.edit', compact(
            'event',
            'venues',
            'organization'
        ));
    }

    public function update(
        Request $request,
        Organization $organization,
        Event $event
    ) {
        $validated = $request->validate([
            'event_name' => ['required', 'string', 'max:150'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => [
                'required',
                'date',
                'after:start_datetime',
            ],
            'status' => [
                'required',
                'string',
                Rule::in(['Planning', 'Ongoing', 'Completed', 'Cancelled']),
            ],
            'venue_id' => [
                'nullable',
                Rule::exists('venues', 'id')
                    ->where('organization_id', $organization->id),
            ],
        ]);

        $event->update($validated);

        return redirect()
            ->route('events.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Data acara berhasil diperbarui.');
    }
}
