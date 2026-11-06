@extends('layouts.ems')

@section('title', 'Kelola Acara | EMS')
@section('page-heading', 'Pengelolaan Acara')

@section('content')

<div style="max-width:1200px; margin:auto;">

    @if (session('success'))
        <div style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0; padding:13px 16px; border-radius:8px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:22px;">

        <div>
            <h2 style="font-size:22px; font-weight:bold;">
                Daftar Acara
            </h2>

            <p style="color:#64748b; font-size:14px; margin-top:5px;">
                Seluruh acara yang terdaftar dalam sistem.
            </p>
        </div>

        <a href="{{ route('events.create') }}"
           style="background:#2F65F6; color:white; padding:11px 17px; border-radius:7px; text-decoration:none; font-size:14px;">
            + Tambah Acara
        </a>

    </div>

    <div style="background:white; border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">

        <div style="padding:18px 20px; border-bottom:1px solid #E2E8F0;">
            <h3 style="font-weight:bold;">
                Data Acara
            </h3>

            <p style="color:#64748b; font-size:13px; margin-top:5px;">
                Informasi acara berdasarkan database EMS.
            </p>
        </div>

        @if ($events->isEmpty())

            <div style="padding:50px 20px; text-align:center;">

                <h3 style="font-weight:bold; color:#334155;">
                    Belum ada acara
                </h3>

                <p style="color:#64748b; margin-top:8px;">
                    Belum terdapat data acara di dalam sistem.
                    Tambahkan acara untuk mulai mengelola kebutuhan operasionalnya.
                </p>

                <a href="{{ route('events.create') }}"
                   style="display:inline-block; margin-top:18px; background:#2F65F6; color:white; padding:10px 16px; border-radius:7px; text-decoration:none;">
                    Tambah Acara
                </a>

            </div>

        @else

            <div style="overflow-x:auto;">

                <table style="width:100%; border-collapse:collapse; min-width:750px; font-size:14px;">

                    <thead style="background:#F8FAFC;">

                        <tr>
                            <th style="padding:14px 16px; text-align:left; color:#64748b;">Nama Acara</th>
                            <th style="padding:14px 16px; text-align:left; color:#64748b;">Waktu Mulai</th>
                            <th style="padding:14px 16px; text-align:left; color:#64748b;">Waktu Selesai</th>
                            <th style="padding:14px 16px; text-align:left; color:#64748b;">Venue</th>
                            <th style="padding:14px 16px; text-align:left; color:#64748b;">Status</th>
                            <th style="padding:14px 16px; text-align:right; color:#64748b;">Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($events as $event)

                            <tr style="border-top:1px solid #E2E8F0;">

                                <td style="padding:15px 16px; font-weight:600;">
                                    {{ $event->event_name }}
                                </td>

                                <td style="padding:15px 16px;">
                                    {{ $event->start_datetime?->format('d M Y, H:i') ?? '—' }}
                                </td>

                                <td style="padding:15px 16px;">
                                    {{ $event->end_datetime?->format('d M Y, H:i') ?? '—' }}
                                </td>

                                <td style="padding:15px 16px;">
                                    {{ $event->venue?->venue_name ?? 'Belum ditentukan' }}
                                </td>

                                <td style="padding:15px 16px;">
                                    {{ $event->status }}
                                </td>

                                <td style="padding:15px 16px; text-align:right;">
                                    <a href="{{ route('events.edit', $event) }}"
                                       style="color:#2563EB; font-weight:600; text-decoration:none;">
                                        Edit
                                    </a>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </div>

</div>

@endsection