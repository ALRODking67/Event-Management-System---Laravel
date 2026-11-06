<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventConcept;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventConceptController extends Controller
{
    // Menampilkan daftar acara dan konsepnya
    public function index()
    {
        $events = Event::with(['concept', 'venue'])
            ->orderBy('start_datetime')
            ->get();

        return view('event-concepts.index', compact('events'));
    }

    // Menampilkan form pembuatan konsep
    public function create()
    {
        $events = Event::whereDoesntHave('concept')
            ->orderBy('start_datetime')
            ->get();

        return view('event-concepts.create', compact('events'));
    }

    // Menyimpan konsep baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                'exists:events,id',
                Rule::unique('event_concepts', 'event_id'),
            ],
            'purpose' => ['nullable', 'string'],
            'theme' => ['required', 'string', 'max:150'],
            'target_audience' => ['nullable', 'string'],
            'event_format' => ['nullable', 'string', 'max:100'],
        ]);

        EventConcept::create($validated);

        return redirect()
            ->route('event-concepts.index')
            ->with('success', 'Konsep acara berhasil disimpan.');
    }

    // Menampilkan form edit konsep
    public function edit(EventConcept $eventConcept)
    {
        $eventConcept->load('event');

        return view('event-concepts.edit', compact('eventConcept'));
    }

    // Memperbarui konsep
    public function update(
        Request $request,
        EventConcept $eventConcept
    ) {
        $validated = $request->validate([
            'purpose' => ['nullable', 'string'],
            'theme' => ['required', 'string', 'max:150'],
            'target_audience' => ['nullable', 'string'],
            'event_format' => ['nullable', 'string', 'max:100'],
        ]);

        $eventConcept->update($validated);

        return redirect()
            ->route('event-concepts.index')
            ->with('success', 'Konsep acara berhasil diperbarui.');
    }
}
