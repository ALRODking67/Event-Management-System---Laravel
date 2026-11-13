@extends('layouts.ems')

@section('title', 'Edit Konsep Acara | EMS')
@section('page-heading', 'Edit Konsep Acara')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">

    <a href="{{ route('event-concepts.index', ['organization' => $currentOrganization->slug]) }}"
       style="display: inline-block; margin-bottom: 20px; color: #2563eb; text-decoration: none; font-size: 14px;">
        &larr; Kembali ke daftar konsep
    </a>

    @if ($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong>Periksa kembali data berikut:</strong>
            <ul style="margin: 8px 0 0 20px; list-style: disc;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">

        <div style="padding: 22px 24px; border-bottom: 1px solid #e2e8f0;">
            <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0;">
                Informasi Konsep
            </h3>
            <p style="font-size: 13px; color: #64748b; margin: 6px 0 0;">
                Periksa informasi acara dan perbarui konsepnya.
            </p>
        </div>

        <form action="{{ route('event-concepts.update', ['organization' => $currentOrganization->slug, $eventConcept]) }}"
              method="POST"
              style="padding: 24px;">

            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Nama Acara
                </label>

                <input type="text"
                       value="{{ $eventConcept->event->event_name }}"
                       readonly
                       style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: #f1f5f9; color: #64748b;">

                <p style="font-size: 12px; color: #64748b; margin-top: 6px;">
                    Nama acara berasal dari data acara dan tidak diubah melalui form ini.
                </p>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Tanggal Acara
                </label>

                <input type="text"
                       value="{{ $eventConcept->event->start_datetime?->format('d M Y, H:i') ?? '—' }}"
                       readonly
                       style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: #f1f5f9; color: #64748b;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="purpose" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Tujuan Acara
                </label>

                <textarea id="purpose"
                          name="purpose"
                          rows="3"
                          placeholder="Jelaskan tujuan penyelenggaraan acara..."
                          style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; resize: vertical;">{{ old('purpose', $eventConcept->purpose) }}</textarea>

                @error('purpose')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="theme" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Tema Acara *
                </label>

                <input id="theme"
                       type="text"
                       name="theme"
                       value="{{ old('theme', $eventConcept->theme) }}"
                       maxlength="150"
                       required
                       placeholder="Masukkan tema acara"
                       style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">

                @error('theme')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="target_audience" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Target Audiens
                </label>

                <textarea id="target_audience"
                          name="target_audience"
                          rows="3"
                          placeholder="Jelaskan target audiens acara..."
                          style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; resize: vertical;">{{ old('target_audience', $eventConcept->target_audience) }}</textarea>

                @error('target_audience')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label for="event_format" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Format Acara
                </label>

                <select id="event_format"
                        name="event_format"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: white;">
                    <option value="">Pilih format acara</option>
                    <option value="Offline" @selected(old('event_format', $eventConcept->event_format) === 'Offline')>
                        Offline
                    </option>
                    <option value="Online" @selected(old('event_format', $eventConcept->event_format) === 'Online')>
                        Online
                    </option>
                    <option value="Hybrid" @selected(old('event_format', $eventConcept->event_format) === 'Hybrid')>
                        Hybrid
                    </option>
                </select>

                @error('event_format')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 20px; flex-wrap: wrap;">

                <a href="{{ route('event-concepts.index', ['organization' => $currentOrganization->slug]) }}"
                   style="display: inline-block; padding: 10px 18px; border: 1px solid #cbd5e1; border-radius: 8px; color: #334155; text-decoration: none; font-size: 14px;">
                    Batal
                </a>

                <button type="submit"
                        style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
                    Simpan Perubahan
                </button>

            </div>
        </form>
    </div>
</div>
@endsection