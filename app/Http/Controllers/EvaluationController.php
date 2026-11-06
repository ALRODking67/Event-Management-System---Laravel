<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index()
    {
        $events = Event::with([
            'evaluation',
            'financialTransactions',
        ])
            ->orderByDesc('start_datetime')
            ->get();

        return view('evaluations.index', compact('events'));
    }

    public function create(Event $event)
    {
        $event->load([
            'evaluation',
            'financialTransactions',
        ]);

        return view('evaluations.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'effectiveness_notes' => ['nullable', 'string'],
            'strengths' => ['nullable', 'string'],
            'improvements' => ['nullable', 'string'],
        ]);

        if ($event->evaluation()->exists()) {
            return redirect()
                ->route('evaluations.index')
                ->with('error', 'Evaluasi untuk acara ini sudah tersedia.');
        }

        $validated['evaluated_by'] = auth()->id();
        $validated['evaluated_at'] = now();

        $event->evaluation()->create($validated);

        return redirect()
            ->route('evaluations.index')
            ->with('success', 'Evaluasi acara berhasil disimpan.');
    }

    public function edit(Evaluation $evaluation)
    {
        $evaluation->load('event');

        return view('evaluations.edit', compact('evaluation'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'effectiveness_notes' => ['nullable', 'string'],
            'strengths' => ['nullable', 'string'],
            'improvements' => ['nullable', 'string'],
        ]);

        $validated['evaluated_by'] = auth()->id();
        $validated['evaluated_at'] = now();

        $evaluation->update($validated);

        return redirect()
            ->route('evaluations.index')
            ->with('success', 'Evaluasi acara berhasil diperbarui.');
    }
}