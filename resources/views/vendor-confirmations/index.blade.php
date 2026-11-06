
@extends('layouts.ems')

@section('title', 'Konfirmasi Ketersediaan Vendor')
@section('page-heading', 'Konfirmasi Ketersediaan Vendor')

@section('content')
@php
    $userRole = auth()->user()->role?->role_name;
    $isVendor = $userRole === 'Vendor';
@endphp

<div style="max-width: 1200px; margin: 0 auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 15px; flex-wrap: wrap;">
        <div>
            <h2 style="font-size: 24px; font-weight: 700; color: #1e293b;">
                Konfirmasi Ketersediaan Vendor
            </h2>

            <p style="color: #64748b; margin-top: 5px;">
                {{ $isVendor
                    ? 'Lihat penugasan dan kirim jawaban ketersediaan layanan Anda.'
                    : 'Pantau jawaban ketersediaan vendor dan riwayat konfirmasi setiap penugasan.' }}
            </p>
        </div>

        @if ($isVendor)
            <a href="{{ route('vendor-confirmations.create') }}"
               style="background: #2563eb; color: white; padding: 11px 18px; border-radius: 7px; text-decoration: none; white-space: nowrap;">
                + Beri Jawaban
            </a>
        @endif
    </div>

    @if (session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 7px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($eventVendors as $eventVendor)
        @php
            $latestConfirmation = $eventVendor->confirmations->first();
        @endphp

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 20px; overflow: hidden;">

            <div style="background: #f8fafc; padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b;">
                    {{ $eventVendor->event->event_name ?? 'Acara tidak ditemukan' }}
                </h3>

                <p style="color: #64748b; margin-top: 5px;">
                    Vendor: {{ $eventVendor->vendor->vendor_name ?? 'Vendor tidak ditemukan' }}
                </p>

                <p style="color: #64748b; margin-top: 4px;">
                    Layanan: {{ $eventVendor->service_description }}
                </p>

                <p style="color: #64748b; margin-top: 8px;">
                    Status Penugasan:
                    <strong style="color: #334155;">
                        {{ $eventVendor->assignment_status }}
                    </strong>
                </p>
            </div>

            @if ($eventVendor->confirmations->isEmpty())
                <div style="padding: 20px; color: #64748b;">
                    Belum ada jawaban ketersediaan untuk penugasan ini.

                    @if ($isVendor && $eventVendor->assignment_status === 'Requested')
                        <div style="margin-top: 12px;">
                            <a href="{{ route('vendor-confirmations.create') }}"
                               style="color: #2563eb; font-weight: 600; text-decoration: none;">
                                Berikan jawaban sekarang
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 650px;">
                        <thead>
                            <tr style="background: #ffffff; text-align: left;">
                                <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                    Status Ketersediaan
                                </th>

                                <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                    Waktu Jawaban
                                </th>

                                <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                    Catatan
                                </th>

                                <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                    Keterangan
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($eventVendor->confirmations as $confirmation)
                                @php
                                    $isLatest = $latestConfirmation
                                        && $latestConfirmation->id === $confirmation->id;
                                @endphp

                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                        @if ($confirmation->availability_status === 'Available')
                                            <span style="background: #dcfce7; color: #166534; padding: 5px 9px; border-radius: 5px;">
                                                Tersedia
                                            </span>
                                        @elseif ($confirmation->availability_status === 'Unavailable')
                                            <span style="background: #fee2e2; color: #991b1b; padding: 5px 9px; border-radius: 5px;">
                                                Tidak Tersedia
                                            </span>
                                        @else
                                            {{ $confirmation->availability_status }}
                                        @endif
                                    </td>

                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                        {{ $confirmation->confirmed_at
                                            ? $confirmation->confirmed_at->format('d-m-Y H:i')
                                            : 'Belum dikonfirmasi' }}
                                    </td>

                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                        {{ $confirmation->notes ?: '—' }}
                                    </td>

                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                                        @if ($isLatest)
                                            <span style="background: #dbeafe; color: #1d4ed8; padding: 5px 9px; border-radius: 5px; font-weight: 600;">
                                                Jawaban Terbaru
                                            </span>
                                        @else
                                            <span style="color: #64748b;">
                                                Riwayat
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (
                $isVendor &&
                $eventVendor->assignment_status === 'Requested' &&
                !$eventVendor->confirmations->isEmpty()
            )
                <div style="padding: 14px 20px; border-top: 1px solid #e2e8f0; color: #64748b; font-size: 14px;">
                    Anda sudah mengirim jawaban. Jika ingin mengubah jawaban, kirim jawaban baru melalui menu Beri Jawaban selama penugasan masih Requested.
                </div>
            @endif

        </div>
    @empty
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 30px; text-align: center; color: #64748b;">
            {{ $isVendor
                ? 'Belum ada penugasan layanan untuk akun Vendor Anda.'
                : 'Belum ada penugasan vendor. Tambahkan penugasan vendor terlebih dahulu.' }}
        </div>
    @endforelse

</div>
@endsection
