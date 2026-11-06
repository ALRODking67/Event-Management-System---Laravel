@extends('layouts.ems')

@section('title', 'Edit Acara | EMS')
@section('page-heading', 'Edit Acara')

@section('content')

<div style="max-width:900px; margin:auto;">

    <a href="{{ route('events.index', ['organization' => $organization->slug]) }}"
       style="display:inline-block; margin-bottom:20px; color:#2563EB; text-decoration:none; font-size:14px;">
        &larr; Kembali ke daftar acara
    </a>

    @if ($errors->any())
        <div style="background:#FEF2F2; border:1px solid #FECACA; color:#B91C1C; padding:14px 18px; border-radius:8px; margin-bottom:20px;">

            <strong>Periksa kembali data berikut:</strong>

            <ul style="margin-top:8px; padding-left:20px; list-style:disc;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif

    <div style="background:white; border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">

        <div style="padding:22px 24px; border-bottom:1px solid #E2E8F0;">

            <h3 style="font-size:18px; font-weight:bold;">
                Perbarui Informasi Acara
            </h3>

            <p style="color:#64748b; font-size:13px; margin-top:5px;">
                Ubah informasi acara yang sudah terdaftar dalam sistem.
            </p>

        </div>

        <form action="{{ route('events.update', ['organization' => $organization->slug, 'event' => $event->id]) }}"
              method="POST"
              style="padding:24px;">

            @csrf
            @method('PUT')

            <div style="margin-bottom:22px;">

                <label for="event_name"
                       style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                    Nama Acara *
                </label>

                <input
                    type="text"
                    name="event_name"
                    id="event_name"
                    value="{{ old('event_name', $event->event_name) }}"
                    required
                    maxlength="150"
                    style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px;"
                >

                @error('event_name')
                    <p style="color:#DC2626; font-size:13px; margin-top:5px;">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:20px; margin-bottom:22px;">

                <div>

                    <label for="start_datetime"
                           style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                        Waktu Mulai *
                    </label>

                    <input
                        type="datetime-local"
                        name="start_datetime"
                        id="start_datetime"
                        value="{{ old('start_datetime', $event->start_datetime?->format('Y-m-d\TH:i')) }}"
                        required
                        style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px;"
                    >

                    @error('start_datetime')
                        <p style="color:#DC2626; font-size:13px; margin-top:5px;">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div>

                    <label for="end_datetime"
                           style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                        Waktu Selesai *
                    </label>

                    <input
                        type="datetime-local"
                        name="end_datetime"
                        id="end_datetime"
                        value="{{ old('end_datetime', $event->end_datetime?->format('Y-m-d\TH:i')) }}"
                        required
                        style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px;"
                    >

                    @error('end_datetime')
                        <p style="color:#DC2626; font-size:13px; margin-top:5px;">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

            <div style="margin-bottom:22px;">

                <label for="venue_id"
                       style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                    Venue
                </label>

                <select
                    name="venue_id"
                    id="venue_id"
                    style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px; background:white;"
                >

                    <option value="">Belum ditentukan</option>

                    @foreach ($venues as $venue)
                        <option
                            value="{{ $venue->id }}"
                            @selected(old('venue_id', $event->venue_id) == $venue->id)
                        >
                            {{ $venue->venue_name }}
                        </option>
                    @endforeach

                </select>

                @error('venue_id')
                    <p style="color:#DC2626; font-size:13px; margin-top:5px;">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div style="margin-bottom:25px;">

                <label for="status"
                       style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                    Status Acara *
                </label>

                <select
                    name="status"
                    id="status"
                    required
                    style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px; background:white;"
                >

                    @foreach (['Planning', 'Ongoing', 'Completed', 'Cancelled'] as $status)
                        <option
                            value="{{ $status }}"
                            @selected(old('status', $event->status) == $status)
                        >
                            {{ $status }}
                        </option>
                    @endforeach

                </select>

                @error('status')
                    <p style="color:#DC2626; font-size:13px; margin-top:5px;">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div style="display:flex; justify-content:flex-end; flex-wrap:wrap; gap:10px; border-top:1px solid #E2E8F0; padding-top:20px;">

                <a href="{{ route('events.index', ['organization' => $organization->slug]) }}"
                   style="padding:10px 18px; border:1px solid #CBD5E1; border-radius:7px; color:#475569; text-decoration:none; font-size:14px;">
                    Kembali
                </a>

                <button
                    type="submit"
                    style="padding:10px 20px; background:#2F65F6; color:white; border:0; border-radius:7px; font-size:14px; font-weight:600; cursor:pointer;"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection