@extends('layouts.ems')

@section('title', 'Konfirmasi & Negosiasi Vendor')
@section('page-heading', 'Konfirmasi & Negosiasi Vendor')

@section('content')

<div style="max-width: 1400px; margin: 0 auto;">

    {{-- HEADER --}}
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:25px;">

        <div>
            <h2 style="font-size:24px; font-weight:700; color:#1e293b;">
                Konfirmasi & Negosiasi Vendor
            </h2>

            <p style="color:#64748b; margin-top:5px;">
                @if ($role === 'Vendor')
                    Lihat penugasan dan riwayat konfirmasi ketersediaan Anda.
                @else
                    Pantau penawaran, respons, dan kesepakatan vendor dalam organisasi ini.
                @endif
            </p>
        </div>

        @if (in_array($role, ['Admin', 'Staf Operasional']))
            <a href="{{ route('event-vendors.create', ['organization' => $organization->slug]) }}"
               style="background:#2f65f6; color:white; padding:12px 18px; border-radius:7px; text-decoration:none; font-weight:600;">
                + Tambahkan Negosiasi dengan Vendor
            </a>
        @elseif ($role === 'Vendor')
            <a href="{{ route('vendor-confirmations.create', ['organization' => $organization->slug]) }}"
               style="background:#2f65f6; color:white; padding:11px 18px; border-radius:7px; text-decoration:none;">
                + Beri Jawaban Ketersediaan
            </a>
        @endif

    </div>

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div style="background:#dcfce7; color:#166534; padding:14px 16px; border-radius:8px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    @if ($role !== 'Vendor')

        {{-- TABEL ADMIN DAN STAF --}}
        <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">

            <div style="padding:18px 20px; border-bottom:1px solid #e2e8f0;">
                <h3 style="font-size:18px; font-weight:700; color:#1e293b;">
                    Daftar Negosiasi dan Konfirmasi Vendor
                </h3>

                <p style="font-size:13px; color:#64748b; margin-top:5px;">
                    Status diperbarui berdasarkan penawaran dan respons terakhir.
                </p>
            </div>

            @if ($eventVendors->isEmpty())

                <div style="padding:35px; text-align:center; color:#64748b;">
                    <p style="font-weight:600; margin-bottom:8px;">
                        Belum Ada Penugasan Vendor
                    </p>

                    <p style="font-size:13px;">
                        Tambahkan negosiasi dengan vendor untuk memulai proses penawaran.
                    </p>
                </div>

            @else

                <div style="overflow-x:auto;">

                    <table style="width:100%; border-collapse:collapse; min-width:1050px;">

                        <thead>
                            <tr style="background:#f8fafc; text-align:left;">

                                <th style="padding:14px 16px;">No.</th>
                                <th style="padding:14px 16px;">Acara</th>
                                <th style="padding:14px 16px;">Vendor</th>
                                <th style="padding:14px 16px;">Penawaran Terakhir</th>
                                <th style="padding:14px 16px;">Status Negosiasi</th>
                                <th style="padding:14px 16px;">Konfirmasi Ketersediaan</th>
                                <th style="padding:14px 16px;">Aksi</th>

                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($eventVendors as $index => $eventVendor)

                                @php
                                    $latestOffer = $eventVendor->offers
                                        ->sortByDesc('id')
                                        ->first();

                                    $latestConfirmation = $eventVendor->confirmations->first();

                                    $vendorUserIds = $vendorMemberUserIds[$eventVendor->vendor_id] ?? [];

                                    $senderIsVendor = $latestOffer
                                        && in_array(
                                            (int) $latestOffer->offered_by_user_id,
                                            $vendorUserIds,
                                            true
                                        );

                                    $responderIsVendor = $latestOffer
                                        && $latestOffer->responded_by_user_id
                                        && in_array(
                                            (int) $latestOffer->responded_by_user_id,
                                            $vendorUserIds,
                                            true
                                        );

                                    if ($eventVendor->agreement_status === 'agreed') {
                                        $negotiationLabel = 'Sudah Disepakati';
                                        $negotiationColor = '#dcfce7';
                                        $negotiationText = '#166534';
                                    } elseif ($latestOffer && $latestOffer->status === 'Pending') {
                                        if ($senderIsVendor) {
                                            $negotiationLabel = 'Menunggu Respons Admin/Staf';
                                        } else {
                                            $negotiationLabel = 'Menunggu Respons Vendor';
                                        }

                                        $negotiationColor = '#fef3c7';
                                        $negotiationText = '#92400e';
                                    } elseif ($latestOffer && $latestOffer->status === 'Rejected') {
                                        $negotiationLabel = $responderIsVendor
                                            ? 'Ditolak Vendor'
                                            : 'Ditolak Admin/Staf';

                                        $negotiationColor = '#fee2e2';
                                        $negotiationText = '#991b1b';
                                    } elseif (!$latestOffer) {
                                        $negotiationLabel = 'Belum Ada Penawaran';
                                        $negotiationColor = '#f1f5f9';
                                        $negotiationText = '#475569';
                                    } else {
                                        $negotiationLabel = ucfirst(
                                            $eventVendor->agreement_status ?? 'negotiating'
                                        );

                                        $negotiationColor = '#f1f5f9';
                                        $negotiationText = '#475569';
                                    }
                                @endphp

                                <tr style="border-top:1px solid #e2e8f0;">

                                    <td style="padding:14px 16px;">
                                        {{ $index + 1 }}
                                    </td>

                                    <td style="padding:14px 16px;">
                                        <strong>
                                            {{ $eventVendor->event->event_name ?? 'Acara tidak ditemukan' }}
                                        </strong>

                                        <div style="font-size:12px; color:#64748b; margin-top:4px;">
                                            {{ $eventVendor->service_description }}
                                        </div>
                                    </td>

                                    <td style="padding:14px 16px;">
                                        {{ $eventVendor->vendor->vendor_name ?? 'Vendor tidak ditemukan' }}
                                    </td>

                                    <td style="padding:14px 16px;">
                                        @if ($latestOffer && $latestOffer->amount !== null)
                                            <strong>
                                                Rp {{ number_format((float) $latestOffer->amount, 0, ',', '.') }}
                                            </strong>
                                        @else
                                            <span style="color:#94a3b8;">
                                                Belum ada harga
                                            </span>
                                        @endif
                                    </td>

                                    <td style="padding:14px 16px;">
                                        <span style="display:inline-block; background:{{ $negotiationColor }}; color:{{ $negotiationText }}; padding:6px 9px; border-radius:5px; font-size:12px; font-weight:600;">
                                            {{ $negotiationLabel }}
                                        </span>
                                    </td>

                                    <td style="padding:14px 16px;">

                                        @if ($latestConfirmation)
                                            @if ($latestConfirmation->availability_status === 'Available')
                                                <span style="background:#dcfce7; color:#166534; padding:5px 8px; border-radius:5px; font-size:12px;">
                                                    Tersedia
                                                </span>
                                            @else
                                                <span style="background:#fee2e2; color:#991b1b; padding:5px 8px; border-radius:5px; font-size:12px;">
                                                    Tidak Tersedia
                                                </span>
                                            @endif

                                            <div style="font-size:11px; color:#64748b; margin-top:5px;">
                                                {{ $latestConfirmation->confirmed_at?->format('d-m-Y H:i') }}
                                            </div>
                                        @else
                                            <span style="color:#94a3b8; font-size:12px;">
                                                Belum ada konfirmasi
                                            </span>
                                        @endif

                                    </td>

                                    <td style="padding:14px 16px;">

                                        <a href="{{ route('vendor-negotiations.show', [
                                            'organization' => $organization->slug,
                                            'eventVendor' => $eventVendor->id
                                        ]) }}"
                                           style="display:inline-block; background:#2f65f6; color:white; padding:8px 12px; border-radius:6px; text-decoration:none; font-size:12px; font-weight:600;">
                                            Lihat Detail
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    @else

        {{-- TAMPILAN VENDOR --}}
        @forelse ($eventVendors as $eventVendor)

            @php
                $latestConfirmation = $eventVendor->confirmations->first();
            @endphp

            <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:18px; overflow:hidden;">

                <div style="padding:18px 20px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">

                    <h3 style="font-size:18px; font-weight:700; color:#1e293b;">
                        {{ $eventVendor->event->event_name ?? 'Acara tidak ditemukan' }}
                    </h3>

                    <p style="color:#64748b; margin-top:5px;">
                        Layanan: {{ $eventVendor->service_description }}
                    </p>

                    <p style="color:#64748b; margin-top:5px;">
                        Status Penugasan: <strong>{{ $eventVendor->assignment_status }}</strong>
                    </p>

                </div>

                <div style="padding:20px;">

                    @if ($latestConfirmation)

                        <p>
                            Jawaban terakhir:

                            <strong>
                                {{ $latestConfirmation->availability_status === 'Available' ? 'Tersedia' : 'Tidak Tersedia' }}
                            </strong>
                        </p>

                        <p style="font-size:13px; color:#64748b; margin-top:5px;">
                            {{ $latestConfirmation->confirmed_at?->format('d-m-Y H:i') }}
                        </p>

                        @if ($latestConfirmation->notes)
                            <p style="margin-top:8px;">
                                Catatan: {{ $latestConfirmation->notes }}
                            </p>
                        @endif

                    @else

                        <p style="color:#64748b;">
                            Belum ada jawaban ketersediaan.
                        </p>

                    @endif

                    @if ($eventVendor->assignment_status === 'Requested')
                        <a href="{{ route('vendor-confirmations.create', ['organization' => $organization->slug]) }}"
                           style="display:inline-block; margin-top:15px; background:#2f65f6; color:white; padding:9px 14px; border-radius:6px; text-decoration:none;">
                            Beri Jawaban
                        </a>
                    @endif

                </div>

            </div>

        @empty

            <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; padding:30px; text-align:center; color:#64748b;">
                Belum ada penugasan layanan untuk akun Vendor Anda.
            </div>

        @endforelse

    @endif

</div>

@endsection