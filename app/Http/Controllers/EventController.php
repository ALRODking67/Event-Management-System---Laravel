<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Menampilkan daftar acara
    public function index()
    {
        $events = Event::with('venue')
            ->orderBy('start_datetime')
            ->get();

        return view('events.index', compact('events'));
    }

    // Menampilkan form pembuatan acara
    public function create()
    {
        $venues = Venue::orderBy('venue_name')->get();

        return view('events.create', compact('venues'));
    }

    // Menyimpan acara baru
    public function store(Request $request)
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
                'max:30',
                'in:Planning,Ongoing,Completed,Cancelled',
            ],
            'venue_id' => [
                'nullable',
                'exists:venues,id',
            ],
        ]);

        $validated['created_by'] = auth()->id();

        Event::create($validated);

        return redirect()
            ->route('events.index')
            ->with('success', 'Acara berhasil dibuat.');
    }

    // Menampilkan form edit acara
    public function edit(Event $event)
    {
        $venues = Venue::orderBy('venue_name')->get();

        return view('events.edit', compact('event', 'venues'));
    }

    // Memperbarui acara
    public function update(Request $request, Event $event)
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
                'max:30',
                'in:Planning,Ongoing,Completed,Cancelled',
            ],
            'venue_id' => [
                'nullable',
                'exists:venues,id',
            ],
        ]);

        $event->update($validated);

        return redirect()
            ->route('events.index')
            ->with('success', 'Data acara berhasil diperbarui.');
    }
}
