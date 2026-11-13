@extends('layouts.ems')

@section('title', 'Tambah Acara | EMS')
@section('page-heading', 'Tambah Acara')

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
                Informasi Acara
            </h3>

            <p style="color:#64748b; font-size:13px; margin-top:5px;">
                Lengkapi informasi dasar penyelenggaraan acara.
                Kolom bertanda * wajib diisi.
            </p>

        </div>

        <form action="{{ route('events.store', ['organization' => $organization->slug]) }}" method="POST"
              style="padding:24px;">

            @csrf

            <div style="margin-bottom:22px;">

                <label for="event_name"
                       style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                    Nama Acara *
                </label>

                <input id="event_name"
                       type="text"
                       name="event_name"
                       value="{{ old('event_name') }}"
                       maxlength="150"
                       required
                       placeholder="Masukkan nama acara"
                       style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px;">

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

                    <input id="start_datetime"
                           type="datetime-local"
                           name="start_datetime"
                           value="{{ old('start_datetime') }}"
                           required
                           style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px;">

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

                    <input id="end_datetime"
                           type="datetime-local"
                           name="end_datetime"
                           value="{{ old('end_datetime') }}"
                           required
                           style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px;">

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

                <select id="venue_id"
                        name="venue_id"
                        style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px; background:white;">

                    <option value="">Belum menentukan venue</option>

                    @foreach ($venues as $venue)
                        <option value="{{ $venue->id }}"
                            @selected(old('venue_id') == $venue->id)>

                            {{ $venue->venue_name }}

                            @if ($venue->capacity)
                                — Kapasitas {{ number_format($venue->capacity) }} orang
                            @endif

                        </option>
                    @endforeach

                </select>

                @error('venue_id')
                    <p style="color:#DC2626; font-size:13px; margin-top:5px;">
                        {{ $message }}
                    </p>
                @enderror

                @if ($venues->isEmpty())
                    <p style="color:#64748b; font-size:12px; margin-top:7px;">
                        Belum ada venue terdaftar. Acara tetap dapat dibuat
                        tanpa memilih venue.
                    </p>
                @endif

            </div>

            <div style="margin-bottom:25px;">

                <label for="status"
                       style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                    Status Acara *
                </label>

                <select id="status"
                        name="status"
                        required
                        style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px; background:white;">

                    <option value="Planning"
                        @selected(old('status', 'Planning') === 'Planning')>
                        Planning
                    </option>

                    <option value="Ongoing"
                        @selected(old('status') === 'Ongoing')>
                        Ongoing
                    </option>

                    <option value="Completed"
                        @selected(old('status') === 'Completed')>
                        Completed
                    </option>

                    <option value="Cancelled"
                        @selected(old('status') === 'Cancelled')>
                        Cancelled
                    </option>

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
                    Batal
                </a>

                <button type="submit"
                        style="padding:10px 20px; background:#2F65F6; color:white; border:0; border-radius:7px; font-size:14px; font-weight:600; cursor:pointer;">
                    Simpan Acara
                </button>

            </div>

        </form>

    </div>

</div>

@endsection