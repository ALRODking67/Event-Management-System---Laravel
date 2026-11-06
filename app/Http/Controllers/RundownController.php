<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\RundownItem;
use App\Models\User;
use Illuminate\Http\Request;

class RundownController extends Controller
{
    public function index()
    {
        $events = Event::with([
            'rundownItems' => function ($query) {
                $query->orderBy('start_time');
            }
        ])
            ->orderBy('start_datetime')
            ->get();

        return view('rundowns.index', compact('events'));
    }

    public function create()
    {
        $events = Event::orderBy('start_datetime')->get();

        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('rundowns.create', compact('events', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'activity_name' => ['required', 'string', 'max:150'],
            'start_time' => ['required', 'date'],
            'end_time' => [
                'required',
                'date',
                'after:start_time',
            ],
            'location_detail' => ['nullable', 'string', 'max:150'],
            'responsible_user_id' => [
                'nullable',
                'exists:users,id',
            ],
            'notes' => ['nullable', 'string'],
        ]);

        // Rundown baru secara default belum dipublikasikan.
        $validated['is_published'] = false;

        RundownItem::create($validated);

        return redirect()
            ->route('rundowns.index')
            ->with('success', 'Item rundown berhasil ditambahkan.');
    }

    public function edit(RundownItem $rundown)
    {
        $rundown->load('event');

        $events = Event::orderBy('start_datetime')->get();

        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('rundowns.edit', compact(
            'rundown',
            'events',
            'users'
        ));
    }

    public function update(Request $request, RundownItem $rundown)
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'activity_name' => ['required', 'string', 'max:150'],
            'start_time' => ['required', 'date'],
            'end_time' => [
                'required',
                'date',
                'after:start_time',
            ],
            'location_detail' => ['nullable', 'string', 'max:150'],
            'responsible_user_id' => [
                'nullable',
                'exists:users,id',
            ],
            'notes' => ['nullable', 'string'],
        ]);

        $rundown->update($validated);

        return redirect()
            ->route('rundowns.index')
            ->with('success', 'Item rundown berhasil diperbarui.');
    }

    public function togglePublish(RundownItem $rundown)
    {
        $rundown->update([
            'is_published' => !$rundown->is_published,
        ]);

        $message = $rundown->is_published
            ? 'Rundown berhasil dipublikasikan.'
            : 'Publikasi rundown berhasil dibatalkan.';

        return redirect()
            ->route('rundowns.index')
            ->with('success', $message);
    }
}
