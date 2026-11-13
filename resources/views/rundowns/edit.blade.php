@extends('layouts.ems')

@section('title', 'Edit Rundown | EMS')
@section('page-heading', 'Edit Rundown Acara')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">

    <a href="{{ route('rundowns.index', ['organization' => $currentOrganization->slug]) }}"
       style="display: inline-block; margin-bottom: 20px; color: #2563eb; text-decoration: none; font-size: 14px;">
        &larr; Kembali ke daftar rundown
    </a>

    @if ($errors->any())
        <div style="background: #fef2f2; color: #991b1b; padding: 14px 18px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #fecaca;">
            <strong>Periksa kembali data berikut:</strong>
            <ul style="margin: 8px 0 0 20px; list-style: disc;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">

        <div style="padding: 22px 24px; border-bottom: 1px solid #e2e8f0;">
            <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;">
                Informasi Rundown
            </h2>
            <p style="font-size: 13px; color: #64748b; margin: 6px 0 0;">
                Perbarui jadwal aktivitas dan informasi penanggung jawab.
            </p>
        </div>

        <form action="{{ route('rundowns.update', ['organization' => $currentOrganization->slug, $rundown->id]) }}"
              method="POST"
              style="padding: 24px;">

            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label for="event_id" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Acara *
                </label>

                <select id="event_id" name="event_id" required
                        style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: white;">
                    @foreach ($events as $event)
                        <option value="{{ $event->id }}"
                            {{ old('event_id', $rundown->event_id) == $event->id ? 'selected' : '' }}>
                            {{ $event->event_name }}
                        </option>
                    @endforeach
                </select>

                @error('event_id')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="activity_name" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Nama Aktivitas *
                </label>

                <input id="activity_name" type="text" name="activity_name"
                       value="{{ old('activity_name', $rundown->activity_name) }}"
                       required maxlength="150"
                       style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">

                @error('activity_name')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="start_time" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Waktu Mulai *
                </label>

                <input id="start_time" type="datetime-local" name="start_time"
                       value="{{ old('start_time', $rundown->start_time->format('Y-m-d\TH:i')) }}"
                       required
                       style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">

                @error('start_time')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="end_time" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Waktu Selesai *
                </label>

                <input id="end_time" type="datetime-local" name="end_time"
                       value="{{ old('end_time', $rundown->end_time->format('Y-m-d\TH:i')) }}"
                       required
                       style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">

                @error('end_time')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="location_detail" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Detail Lokasi
                </label>

                <input id="location_detail" type="text" name="location_detail"
                       value="{{ old('location_detail', $rundown->location_detail) }}"
                       maxlength="150"
                       style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">

                @error('location_detail')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="responsible_user_id" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Penanggung Jawab
                </label>

                <select id="responsible_user_id" name="responsible_user_id"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: white;">
                    <option value="">-- Tidak ditentukan --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                            {{ old('responsible_user_id', $rundown->responsible_user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>

                @error('responsible_user_id')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label for="notes" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    Catatan
                </label>

                <textarea id="notes" name="notes" rows="4"
                          style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; resize: vertical;">{{ old('notes', $rundown->notes) }}</textarea>

                @error('notes')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 20px; flex-wrap: wrap;">
                <a href="{{ route('rundowns.index', ['organization' => $currentOrganization->slug]) }}"
                   style="display: inline-block; padding: 10px 18px; border: 1px solid #cbd5e1; border-radius: 8px; color: #334155; text-decoration: none; font-size: 14px;">
                    Batal
                </a>

                <button type="submit"
                        style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600;">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection