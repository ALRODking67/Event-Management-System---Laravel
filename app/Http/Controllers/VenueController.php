<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index(Organization $organization)
    {
        $venues = $organization->venues()
            ->orderBy('venue_name')
            ->get();

        return view('venues.index', compact('venues', 'organization'));
    }

    public function create(Organization $organization)
    {
        return view('venues.create', compact('organization'));
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'venue_name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'electricity_info' => ['nullable', 'string'],
            'access_info' => ['nullable', 'string'],
            'security_info' => ['nullable', 'string'],
            'parking_info' => ['nullable', 'string'],
        ]);

        $organization->venues()->create($validated);

        return redirect()
            ->route('venues.index', ['organization' => $organization->slug])
            ->with('success', 'Venue berhasil ditambahkan.');
    }

    public function edit(Organization $organization, Venue $venue)
    {
        return view('venues.edit', compact('venue', 'organization'));
    }

    public function update(
        Request $request,
        Organization $organization,
        Venue $venue
    ) {
        $validated = $request->validate([
            'venue_name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'electricity_info' => ['nullable', 'string'],
            'access_info' => ['nullable', 'string'],
            'security_info' => ['nullable', 'string'],
            'parking_info' => ['nullable', 'string'],
        ]);

        $venue->update($validated);

        return redirect()
            ->route('venues.index', ['organization' => $organization->slug])
            ->with('success', 'Data venue berhasil diperbarui.');
    }
}
