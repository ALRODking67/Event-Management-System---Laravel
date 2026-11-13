@extends('layouts.ems')

@section('title', 'Edit Evaluasi Acara')
@section('page-heading', 'Edit Evaluasi Acara')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">

    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b;">
            Edit Evaluasi Acara
        </h2>

        <p style="color: #64748b; margin-top: 5px;">
            Perbarui hasil evaluasi berdasarkan pelaksanaan acara.
        </p>
    </div>

    <div style="background: white; padding: 25px; border: 1px solid #e2e8f0; border-radius: 10px;">

        <div style="margin-bottom: 20px; color: #334155;">
            <strong>Nama Acara:</strong>
            {{ $evaluation->event->event_name }}
        </div>

        <form action="{{ route('evaluations.update', [
            'organization' => $organization->slug,
            'evaluation' => $evaluation->id
        ]) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label for="effectiveness_notes" style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Catatan Efektivitas
                </label>

                <textarea name="effectiveness_notes" id="effectiveness_notes" rows="4"
                    style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px;">{{ old('effectiveness_notes', $evaluation->effectiveness_notes) }}</textarea>

                @error('effectiveness_notes')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="strengths" style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Kelebihan Acara
                </label>

                <textarea name="strengths" id="strengths" rows="4"
                    style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px;">{{ old('strengths', $evaluation->strengths) }}</textarea>

                @error('strengths')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 25px;">
                <label for="improvements" style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Hal yang Perlu Diperbaiki
                </label>

                <textarea name="improvements" id="improvements" rows="4"
                    style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px;">{{ old('improvements', $evaluation->improvements) }}</textarea>

                @error('improvements')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="submit"
                    style="background: #2f65f6; color: white; padding: 10px 20px; border: 0; border-radius: 6px; cursor: pointer;">
                    Simpan Perubahan
                </button>

                <a href="{{ route('evaluations.index', ['organization' => $organization->slug]) }}"
                    style="background: #e2e8f0; color: #334155; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>
@endsection
