@extends('layouts.ems')

@section('title', 'Penugasan Vendor')
@section('page-heading', 'Penugasan Vendor')

@section('content')
<div>

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
        <p style="color: #64748b; margin: 0;">
            Kelola penugasan vendor untuk setiap acara.
        </p>

        <a href="{{ route('event-vendors.create') }}"
            style="background: #2f65f6; color: white; padding: 11px 18px; border-radius: 7px; text-decoration: none; font-weight: 600;">
            + Tambah Penugasan
        </a>
    </div>

    @if (session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 14px 16px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($events as $event)
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 25px; overflow: hidden;">

            <div style="background: #f8fafc; padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                <h2 style="font-size: 18px; font-weight: bold; color: #1e293b; margin: 0;">
                    {{ $event->event_name }}
                </h2>

                <p style="font-size: 13px; color: #64748b; margin-top: 5px;">
                    {{ $event->start_datetime }}
                </p>
            </div>

            @if ($event->vendorAssignments->isEmpty())
                <div style="padding: 20px; color: #64748b;">
                    Belum ada vendor yang ditugaskan untuk acara ini.
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 750px;">
                        <thead>
                            <tr style="background: #f8fafc; text-align: left;">
                                <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">Vendor</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">Deskripsi Layanan</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">Biaya Kesepakatan</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">Status</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($event->vendorAssignments as $assignment)
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 600;">
                                        {{ $assignment->vendor->vendor_name ?? 'Vendor tidak ditemukan' }}
                                    </td>

                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                        {{ $assignment->service_description }}
                                    </td>

                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                        Rp {{ number_format($assignment->agreed_cost, 0, ',', '.') }}
                                    </td>

                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                        {{ $assignment->assignment_status }}
                                    </td>

                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                        <a href="{{ route('event-vendors.edit', $assignment->id) }}"
                                            style="color: #2563eb; text-decoration: none; font-weight: 600;">
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
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 30px; text-align: center; color: #64748b;">
            Belum ada data acara.
        </div>
    @endforelse

</div>
@endsection