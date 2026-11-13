<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use App\Models\RundownItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RundownController extends Controller
{
    public function index(Organization $organization)
    {
        $events = $organization->events()
            ->with([
                'rundownItems' => function ($query) {
                    $query->orderBy('start_time');
                }
            ])
            ->orderBy('start_datetime')
            ->get();

        return view('rundowns.index', compact('events'));
    }

    public function create(Organization $organization)
    {
        $events = $organization->events()
            ->orderBy('start_datetime')
            ->get();

        $users = User::where('is_active', true)
            ->whereHas('organizationMemberships', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->orderBy('name')
            ->get();

        return view('rundowns.create', compact('events', 'users'));
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'event_id' => [
                'required',
                Rule::exists('events', 'id')
                    ->where('organization_id', $organization->id),
            ],
            'activity_name' => ['required', 'string', 'max:150'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'location_detail' => ['nullable', 'string', 'max:150'],
            'responsible_user_id' => [
                'nullable',
                Rule::exists('organization_members', 'user_id')
                    ->where('organization_id', $organization->id),
            ],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['is_published'] = false;

        RundownItem::create($validated);

        return redirect()
            ->route('rundowns.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Rundown berhasil ditambahkan.');
    }

    public function edit(
        Organization $organization,
        int $rundown
    ) {
        $rundown = RundownItem::whereKey($rundown)
            ->whereHas('event', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->with('event')
            ->firstOrFail();

        $events = $organization->events()
            ->orderBy('start_datetime')
            ->get();

        $users = User::where('is_active', true)
            ->whereHas('organizationMemberships', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->orderBy('name')
            ->get();

        return view('rundowns.edit', compact('rundown', 'events', 'users'));
    }

    public function update(
        Request $request,
        Organization $organization,
        int $rundown
    ) {
        $rundown = RundownItem::whereKey($rundown)
            ->whereHas('event', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->firstOrFail();

        $validated = $request->validate([
            'event_id' => [
                'required',
                Rule::exists('events', 'id')
                    ->where('organization_id', $organization->id),
            ],
            'activity_name' => ['required', 'string', 'max:150'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'location_detail' => ['nullable', 'string', 'max:150'],
            'responsible_user_id' => [
                'nullable',
                Rule::exists('organization_members', 'user_id')
                    ->where('organization_id', $organization->id),
            ],
            'notes' => ['nullable', 'string'],
        ]);

        $rundown->update($validated);

        return redirect()
            ->route('rundowns.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Rundown berhasil diperbarui.');
    }

    public function togglePublish(
        Organization $organization,
        int $rundown
    ) {
        $rundown = RundownItem::whereKey($rundown)
            ->whereHas('event', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->firstOrFail();

        $rundown->update([
            'is_published' => !$rundown->is_published,
        ]);

        return redirect()
            ->route('rundowns.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Status publikasi rundown berhasil diubah.');
    }
}
