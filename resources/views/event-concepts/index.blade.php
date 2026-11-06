@extends('layouts.ems')

@section('title', 'Kelola Konsep Acara | EMS')
@section('page-heading', 'Kelola Konsep Acara')

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
                Daftar Konsep Acara
            </h2>

            <p style="color:#64748b; font-size:14px; margin-top:5px;">
                Data acara diambil dari database EMS.
            </p>
        </div>

        <a href="{{ route('event-concepts.create') }}"
           style="background:#2F65F6; color:white; padding:11px 17px; border-radius:7px; text-decoration:none; font-size:14px;">
            + Buat Konsep
        </a>

    </div>

    <div style="background:white; border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">

        @if ($events->isEmpty())

            <div style="padding:50px 20px; text-align:center;">

                <h3 style="font-weight:bold; color:#334155;">
                    Belum ada data acara
                </h3>

                <p style="color:#64748b; margin-top:8px;">
                    Belum ada acara yang dapat ditampilkan.
                    Data acara perlu tersedia terlebih dahulu
                    sebelum konsepnya dapat dibuat.
                </p>

            </div>

        @else

            <div style="overflow-x:auto;">

                <table style="width:100%; border-collapse:collapse; min-width:850px; font-size:14px;">

                    <thead style="background:#F8FAFC;">

                        <tr>
                            <th style="padding:14px 16px; text-align:left; color:#64748b;">Nama Acara</th>
                            <th style="padding:14px 16px; text-align:left; color:#64748b;">Tanggal</th>
                            <th style="padding:14px 16px; text-align:left; color:#64748b;">Tema</th>
                            <th style="padding:14px 16px; text-align:left; color:#64748b;">Format</th>
                            <th style="padding:14px 16px; text-align:left; color:#64748b;">Status Konsep</th>
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
                                    {{ $event->concept?->theme ?? 'Belum diisi' }}
                                </td>

                                <td style="padding:15px 16px;">
                                    {{ $event->concept?->event_format ?? '—' }}
                                </td>

                                <td style="padding:15px 16px;">

                                    @if ($event->concept)

                                        <span style="display:inline-block; background:#DCFCE7; color:#15803D; padding:5px 11px; border-radius:20px; font-size:12px; font-weight:600;">
                                            Sudah dibuat
                                        </span>

                                    @else

                                        <span style="display:inline-block; background:#FEF3C7; color:#A16207; padding:5px 11px; border-radius:20px; font-size:12px; font-weight:600;">
                                            Belum dibuat
                                        </span>

                                    @endif

                                </td>

                                <td style="padding:15px 16px; text-align:right;">

                                    @if ($event->concept)

                                        <a href="{{ route('event-concepts.edit', $event->concept) }}"
                                           style="color:#2563EB; font-weight:600; text-decoration:none;">
                                            Edit
                                        </a>

                                    @else

                                        <a href="{{ route('event-concepts.create', ['event_id' => $event->id]) }}"
                                           style="color:#2563EB; font-weight:600; text-decoration:none;">
                                            Buat Konsep
                                        </a>

                                    @endif

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