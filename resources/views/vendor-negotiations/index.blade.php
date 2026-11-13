@extends('layouts.ems')

@section('title', 'Negosiasi Vendor')
@section('page-heading', 'Negosiasi Vendor')

@section('content')
<div>
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:22px;">
        <div>
            <p style="color:#64748b; margin:0;">
                Kelola proses penawaran dan kesepakatan vendor di {{ $organization->name }}.
            </p>
        </div>

        @if ($roleName !== 'Vendor')
            <a href="{{ route('event-vendors.create', ['organization' => $organization->slug]) }}"
               style="display:inline-block; background:#2f65f6; color:white; padding:11px 18px; border-radius:7px; text-decoration:none; font-weight:600;">
                + Buat Penugasan Vendor
            </a>
        @endif
    </div>

    @if (session('success'))
        <div style="background:#dcfce7; color:#166534; padding:14px 16px; border-radius:8px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    @if ($assignments->isEmpty())
        <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; padding:35px; text-align:center;">
            <h3 style="color:#1e293b; margin:0 0 8px;">Belum Ada Negosiasi</h3>
            <p style="color:#64748b; margin:0;">
                Belum ada penugasan vendor yang dapat ditampilkan.
            </p>
        </div>
    @else
        <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; min-width:850px;">
                    <thead>
                        <tr style="background:#f8fafc; text-align:left;">
                            <th style="padding:13px 16px;">No.</th>
                            <th style="padding:13px 16px;">Acara</th>
                            <th style="padding:13px 16px;">Vendor</th>
                            <th style="padding:13px 16px;">Penawaran Terakhir</th>
                            <th style="padding:13px 16px;">Status Negosiasi</th>
                            <th style="padding:13px 16px;">Status Penugasan</th>
                            <th style="padding:13px 16px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($assignments as $index => $assignment)
                            @php
                                $latestOffer = $assignment->offers
                                    ->sortByDesc('id')
                                    ->first();

                                $pendingOffer = $assignment->offers
                                    ->where('status', 'Pending')
                                    ->sortByDesc('id')
                                    ->first();

                                // Identitas Vendor yang sedang login.
                                $vendorUserIds = $assignment->vendor
                                    ? $assignment->vendor->user()->pluck('id')->map(fn ($id) => (int) $id)->all()
                                    : [];

                                $latestOfferSentByVendor = $latestOffer
                                    && in_array(
                                        (int) $latestOffer->offered_by_user_id,
                                        $vendorUserIds,
                                        true
                                    );

                                if ($assignment->agreement_status === 'agreed') {
                                    $negotiationLabel = 'Sudah Disepakati';
                                    $negotiationColor = '#dcfce7';
                                    $negotiationText = '#166534';

                                } elseif ($pendingOffer) {
                                    if ($latestOfferSentByVendor) {
                                        $negotiationLabel = 'Menunggu Respons Admin/Staf';
                                    } else {
                                        $negotiationLabel = 'Menunggu Respons Anda';
                                    }

                                    $negotiationColor = '#fef3c7';
                                    $negotiationText = '#92400e';

                                } elseif (
                                    !$latestOffer &&
                                    $assignment->assignment_status === 'Requested'
                                ) {
                                    $negotiationLabel = 'Menunggu Respons Anda';
                                    $negotiationColor = '#fef3c7';
                                    $negotiationText = '#92400e';

                                } elseif ($latestOffer && $latestOffer->status === 'Rejected') {
                                    $negotiationLabel = 'Anda Menolak Tawaran';
                                        

                                    $negotiationColor = '#fee2e2';
                                    $negotiationText = '#991b1b';

                                } else {
                                    $negotiationLabel = 'Belum Ada Penawaran';
                                    $negotiationColor = '#f1f5f9';
                                    $negotiationText = '#475569';
                                }
                            @endphp

                            <tr style="border-top:1px solid #e2e8f0;">
                                <td style="padding:13px 16px;">{{ $index + 1 }}</td>

                                <td style="padding:13px 16px;">
                                    <strong>{{ $assignment->event->event_name ?? 'Acara tidak ditemukan' }}</strong>
                                </td>

                                <td style="padding:13px 16px;">
                                    {{ $assignment->vendor->vendor_name ?? 'Vendor tidak ditemukan' }}
                                </td>

                                <td style="padding:13px 16px;">
                                    @if ($latestOffer && $latestOffer->amount !== null)
                                        Rp {{ number_format((float) $latestOffer->amount, 0, ',', '.') }}
                                    @else
                                        <span style="color:#94a3b8;">Belum ada harga</span>
                                    @endif
                                </td>

                                <td style="padding:13px 16px;">
                                    <span style="display:inline-block; background:{{ $negotiationColor }}; color:{{ $negotiationText }}; padding:5px 9px; border-radius:5px; font-size:12px; font-weight:600;">
                                        {{ $negotiationLabel }}
                                    </span>
                                </td>

                                <td style="padding:13px 16px;">
                                    {{ $assignment->assignment_status }}
                                </td>

                                <td style="padding:13px 16px;">
                                    <a href="{{ route('vendor-negotiations.show', [
                                        'organization' => $organization->slug,
                                        'eventVendor' => $assignment->id
                                    ]) }}"
                                       style="display:inline-block; color:#2563eb; text-decoration:none; font-weight:600;">
                                        Lihat Negosiasi
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection