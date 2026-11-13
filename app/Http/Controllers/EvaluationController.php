<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Event;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    /**
     * Menampilkan evaluasi seluruh acara dalam organisasi aktif.
     */
    public function index(Organization $organization)
    {
        $events = $organization->events()
            ->with([
                'evaluation',
                'financialTransactions',
            ])
            ->orderByDesc('start_datetime')
            ->get();

        return view(
            'evaluations.index',
            compact('organization', 'events')
        );
    }

    /**
     * Menampilkan form pembuatan evaluasi.
     */
    public function create(Organization $organization, Event $event)
    {
        $event->load([
            'evaluation',
            'financialTransactions',
        ]);

        return view(
            'evaluations.create',
            compact('organization', 'event')
        );
    }

    /**
     * Menyimpan evaluasi acara.
     */
    public function store(
        Request $request,
        Organization $organization,
        Event $event
    ) {
        $validated = $request->validate([
            'effectiveness_notes' => ['nullable', 'string'],
            'strengths' => ['nullable', 'string'],
            'improvements' => ['nullable', 'string'],
        ]);

        if ($event->evaluation()->exists()) {
            return redirect()
                ->route('evaluations.index', [
                    'organization' => $organization->slug,
                ])
                ->with('error', 'Evaluasi untuk acara ini sudah tersedia.');
        }

        $validated['evaluated_by'] = auth()->id();
        $validated['evaluated_at'] = now();

        $event->evaluation()->create($validated);

        return redirect()
            ->route('evaluations.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Evaluasi acara berhasil disimpan.');
    }

    /**
     * Menampilkan form edit evaluasi.
     */
    public function edit(
        Organization $organization,
        Evaluation $evaluation
    ) {
        $evaluation->load('event');

        return view(
            'evaluations.edit',
            compact('organization', 'evaluation')
        );
    }

    /**
     * Memperbarui evaluasi.
     */
    public function update(
        Request $request,
        Organization $organization,
        Evaluation $evaluation
    ) {
        $validated = $request->validate([
            'effectiveness_notes' => ['nullable', 'string'],
            'strengths' => ['nullable', 'string'],
            'improvements' => ['nullable', 'string'],
        ]);

        $validated['evaluated_by'] = auth()->id();
        $validated['evaluated_at'] = now();

        $evaluation->update($validated);

        return redirect()
            ->route('evaluations.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Evaluasi acara berhasil diperbarui.');
    }
}
