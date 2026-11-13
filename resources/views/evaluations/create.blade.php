@extends('layouts.ems')

@section('title', 'Buat Evaluasi Acara')
@section('page-heading', 'Buat Evaluasi Acara')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">

    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b;">
            Buat Evaluasi Acara
        </h2>

        <p style="color: #64748b; margin-top: 5px;">
            Catat hasil evaluasi berdasarkan pelaksanaan acara.
        </p>
    </div>

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 25px;">

        <form action="{{ route('evaluations.store', [
            'organization' => $organization->slug,
            'event' => $event->id
        ]) }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px;">
                <label for="event_name" style="display: block; font-weight: 600; margin-bottom: 7px; color: #334155;">
                    Nama Acara
                </label>

                <input
                    type="text"
                    id="event_name"
                    value="{{ $event->event_name }}"
                    readonly
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc;"
                >
            </div>

            <div style="margin-bottom: 20px;">
                <label for="effectiveness_notes" style="display: block; font-weight: 600; margin-bottom: 7px; color: #334155;">
                    Catatan Efektivitas
                </label>

                <textarea name="effectiveness_notes" id="effectiveness_notes" rows="4"
                    placeholder="Tuliskan hasil dan efektivitas pelaksanaan acara..."
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">{{ old('effectiveness_notes') }}</textarea>

                @error('effectiveness_notes')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="strengths" style="display: block; font-weight: 600; margin-bottom: 7px; color: #334155;">
                    Kelebihan Acara
                </label>

                <textarea name="strengths" id="strengths" rows="4"
                    placeholder="Tuliskan hal-hal yang berjalan dengan baik..."
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">{{ old('strengths') }}</textarea>

                @error('strengths')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 25px;">
                <label for="improvements" style="display: block; font-weight: 600; margin-bottom: 7px; color: #334155;">
                    Hal yang Perlu Diperbaiki
                </label>

                <textarea name="improvements" id="improvements" rows="4"
                    placeholder="Tuliskan kekurangan dan saran perbaikan..."
                    style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">{{ old('improvements') }}</textarea>

                @error('improvements')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="submit"
                    style="background: #2f65f6; color: white; padding: 10px 18px; border: none; border-radius: 6px; cursor: pointer;">
                    Simpan Evaluasi
                </button>

                <a href="{{ route('evaluations.index', ['organization' => $organization->slug]) }}"
                    style="background: #e2e8f0; color: #334155; padding: 10px 18px; border-radius: 6px; text-decoration: none;">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>
@endsection
