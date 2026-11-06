@extends('layouts.ems')

@section('title', 'Penugasan Peralatan Acara')

@section('page-heading', 'Penugasan Peralatan Acara')

@section('content')
<div>

    @if (session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 14px 18px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #bbf7d0;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 24px;">

        <div>
            <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 5px;">
                Daftar Penugasan Peralatan
            </h2>

            <p style="font-size: 14px; color: #64748b;">
                Kelola penggunaan peralatan untuk setiap acara.
            </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('equipment.index') }}"
               style="display: inline-block; padding: 11px 16px; border: 1px solid #cbd5e1; border-radius: 7px; color: #475569; text-decoration: none; font-size: 14px;">
                Lihat Inventaris
            </a>

            <a href="{{ route('event-equipment.create') }}"
               style="display: inline-block; background: #2f65f6; color: white; padding: 11px 18px; border-radius: 7px; text-decoration: none; font-size: 14px; font-weight: 600;">
                + Tambah Penugasan
            </a>
        </div>
    </div>

    @forelse ($events as $event)

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; overflow: hidden;">

            <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; background: #ffffff;">

                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                    {{ $event->event_name }}
                </h3>

                <p style="font-size: 13px; color: #64748b;">
                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('d M Y, H:i') }}
                </p>

            </div>

            @if ($event->equipmentAssignments->isEmpty())

                <div style="padding: 24px;">
                    <p style="font-size: 14px; color: #64748b;">
                        Belum ada peralatan yang ditugaskan untuk acara ini.
                    </p>
                </div>

            @else

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">

                        <thead>
                            <tr style="background: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase;">
                                <th style="padding: 14px 18px; font-weight: 700;">No.</th>
                                <th style="padding: 14px 18px; font-weight: 700;">Peralatan</th>
                                <th style="padding: 14px 18px; font-weight: 700;">Kategori</th>
                                <th style="padding: 14px 18px; font-weight: 700;">Jumlah</th>
                                <th style="padding: 14px 18px; font-weight: 700;">Status Penggunaan</th>
                                <th style="padding: 14px 18px; font-weight: 700;">Catatan Kondisi</th>
                                <th style="padding: 14px 18px; font-weight: 700;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($event->equipmentAssignments as $index => $assignment)

                                <tr style="border-top: 1px solid #e2e8f0; color: #334155;">

                                    <td style="padding: 15px 18px; font-size: 14px;">
                                        {{ $index + 1 }}
                                    </td>

                                    <td style="padding: 15px 18px; font-size: 14px; font-weight: 600;">
                                        {{ $assignment->equipment->equipment_name }}
                                    </td>

                                    <td style="padding: 15px 18px; font-size: 14px;">
                                        {{ $assignment->equipment->category->category_name }}
                                    </td>

                                    <td style="padding: 15px 18px; font-size: 14px;">
                                        {{ $assignment->quantity }}
                                    </td>

                                    <td style="padding: 15px 18px; font-size: 14px;">
                                        <span style="display: inline-block; background: #eff6ff; color: #1d4ed8; padding: 5px 9px; border-radius: 6px; font-weight: 600;">
                                            {{ $assignment->usage_status }}
                                        </span>
                                    </td>

                                    <td style="padding: 15px 18px; font-size: 14px; color: #64748b;">
                                        {{ $assignment->condition_notes ?: '—' }}
                                    </td>

                                    <td style="padding: 15px 18px; font-size: 14px;">
                                        <a href="{{ route('event-equipment.edit', $assignment->id) }}"
                                           style="color: #2f65f6; font-weight: 600; text-decoration: none;">
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

    @empty

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px 24px; text-align: center;">

            <h3 style="font-size: 16px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                Belum Ada Acara
            </h3>

            <p style="font-size: 14px; color: #64748b;">
                Belum ada acara yang tersedia untuk penugasan peralatan.
            </p>

        </div>

    @endforelse

</div>
@endsection