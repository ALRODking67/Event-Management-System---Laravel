@extends('layouts.ems')

@section('title', 'Buat Konsep Acara | EMS')
@section('page-heading', 'Buat Konsep Acara')

@section('content')

<div style="max-width:950px; margin:auto;">

    <a href="{{ route('event-concepts.index') }}"
       style="display:inline-block; margin-bottom:20px; color:#2563EB; text-decoration:none; font-size:14px;">
        &larr; Kembali ke daftar konsep
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

    @if ($events->isEmpty())

        <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:10px; padding:24px;">

            <h3 style="font-weight:bold; color:#92400E;">
                Belum ada acara yang tersedia
            </h3>

            <p style="margin-top:10px; color:#92400E; font-size:14px; line-height:1.7;">
                Konsep harus terhubung dengan acara yang sudah terdaftar
                di tabel events. Saat ini belum ada acara yang bisa
                dipilih, sehingga konsep belum dapat dibuat.
            </p>

            <p style="margin-top:10px; color:#92400E; font-size:14px;">
                Silakan kembali setelah data acara tersedia melalui
                modul pengelolaan acara.
            </p>

            <a href="{{ route('event-concepts.index') }}"
               style="display:inline-block; margin-top:18px; background:white; color:#92400E; padding:10px 16px; border:1px solid #FCD34D; border-radius:7px; text-decoration:none; font-size:14px;">
                Kembali ke Daftar Konsep
            </a>

        </div>

    @else

        <div style="background:white; border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">

            <div style="padding:22px 24px; border-bottom:1px solid #E2E8F0;">

                <h3 style="font-size:18px; font-weight:bold;">
                    Informasi Konsep
                </h3>

                <p style="color:#64748b; font-size:13px; margin-top:5px;">
                    Isi informasi sesuai kebutuhan acara.
                    Kolom bertanda * wajib diisi.
                </p>

            </div>

            <form action="{{ route('event-concepts.store') }}"
                  method="POST"
                  style="padding:24px;">

                @csrf

                <div style="margin-bottom:22px;">

                    <label for="event_id"
                           style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                        Nama Acara *
                    </label>

                    <select id="event_id"
                            name="event_id"
                            required
                            style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px; background:white;">

                        <option value="">Pilih acara</option>

                        @foreach ($events as $event)
                            <option value="{{ $event->id }}"
                                @selected(old('event_id', request('event_id')) == $event->id)>

                                {{ $event->event_name }}
                                — {{ $event->start_datetime?->format('d M Y H:i') ?? 'Tanggal belum tersedia' }}

                            </option>
                        @endforeach

                    </select>

                    @error('event_id')
                        <p style="color:#DC2626; font-size:13px; margin-top:5px;">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div style="margin-bottom:22px;">

                    <label for="purpose"
                           style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                        Tujuan Acara
                    </label>

                    <textarea id="purpose"
                              name="purpose"
                              rows="3"
                              placeholder="Jelaskan tujuan penyelenggaraan acara..."
                              style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px; resize:vertical;">{{ old('purpose') }}</textarea>

                    @error('purpose')
                        <p style="color:#DC2626; font-size:13px; margin-top:5px;">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div style="margin-bottom:22px;">

                    <label for="theme"
                           style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                        Tema Acara *
                    </label>

                    <input id="theme"
                           type="text"
                           name="theme"
                           value="{{ old('theme') }}"
                           maxlength="150"
                           required
                           placeholder="Masukkan tema acara"
                           style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px;">

                    @error('theme')
                        <p style="color:#DC2626; font-size:13px; margin-top:5px;">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div style="margin-bottom:22px;">

                    <label for="target_audience"
                           style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                        Target Audiens
                    </label>

                    <textarea id="target_audience"
                              name="target_audience"
                              rows="3"
                              placeholder="Contoh: mahasiswa, dosen, atau praktisi IT"
                              style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px; resize:vertical;">{{ old('target_audience') }}</textarea>

                    @error('target_audience')
                        <p style="color:#DC2626; font-size:13px; margin-top:5px;">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div style="margin-bottom:25px;">

                    <label for="event_format"
                           style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">
                        Format Acara
                    </label>

                    <select id="event_format"
                            name="event_format"
                            style="width:100%; padding:11px 12px; border:1px solid #CBD5E1; border-radius:7px; background:white;">

                        <option value="">Pilih format acara</option>

                        <option value="Offline"
                            @selected(old('event_format') === 'Offline')>
                            Offline
                        </option>

                        <option value="Online"
                            @selected(old('event_format') === 'Online')>
                            Online
                        </option>

                        <option value="Hybrid"
                            @selected(old('event_format') === 'Hybrid')>
                            Hybrid
                        </option>

                    </select>

                    @error('event_format')
                        <p style="color:#DC2626; font-size:13px; margin-top:5px;">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div style="display:flex; justify-content:flex-end; flex-wrap:wrap; gap:10px; border-top:1px solid #E2E8F0; padding-top:20px;">

                    <a href="{{ route('event-concepts.index') }}"
                       style="padding:10px 18px; border:1px solid #CBD5E1; border-radius:7px; color:#475569; text-decoration:none; font-size:14px;">
                        Batal
                    </a>

                    <button type="submit"
                            style="padding:10px 20px; background:#2F65F6; color:white; border:0; border-radius:7px; font-size:14px; font-weight:600; cursor:pointer;">
                        Simpan Konsep
                    </button>

                </div>

            </form>

        </div>

    @endif

</div>

@endsection