<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventConcept;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventConceptController extends Controller
{
    public function index(Organization $organization)
    {
        $events = $organization->events()
            ->with(['concept', 'venue'])
            ->orderBy('start_datetime')
            ->get();

        return view('event-concepts.index', compact('events'));
    }

    public function create(Organization $organization)
    {
        $events = $organization->events()
            ->whereDoesntHave('concept')
            ->orderBy('start_datetime')
            ->get();

        return view('event-concepts.create', compact('events'));
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                Rule::exists('events', 'id')
                    ->where('organization_id', $organization->id),
                Rule::unique('event_concepts', 'event_id'),
            ],
            'purpose' => ['nullable', 'string'],
            'theme' => ['required', 'string', 'max:150'],
            'target_audience' => ['nullable', 'string'],
            'event_format' => ['nullable', 'string', 'max:100'],
        ]);

        EventConcept::create($validated);

        return redirect()
            ->route('event-concepts.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Konsep acara berhasil disimpan.');
    }

    public function edit(
        Organization $organization,
        int $eventConcept
    ) {
        $eventConcept = EventConcept::whereKey($eventConcept)
            ->whereHas('event', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->with('event')
            ->firstOrFail();

        return view('event-concepts.edit', compact('eventConcept'));
    }

    public function update(
        Request $request,
        Organization $organization,
        int $eventConcept
    ) {
        $eventConcept = EventConcept::whereKey($eventConcept)
            ->whereHas('event', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->firstOrFail();

        $validated = $request->validate([
            'purpose' => ['nullable', 'string'],
            'theme' => ['required', 'string', 'max:150'],
            'target_audience' => ['nullable', 'string'],
            'event_format' => ['nullable', 'string', 'max:100'],
        ]);

        $eventConcept->update($validated);

        return redirect()
            ->route('event-concepts.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Konsep acara berhasil diperbarui.');
    }
}
