@extends('layouts.ems')

@section('title', 'Kelola Vendor & Sumber Daya')
@section('page-heading', 'Kelola Vendor & Sumber Daya')

@section('content')
<div>

    @if (session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 14px; margin-bottom: 20px; border-radius: 8px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <a href="{{ route('vendors.create', ['organization' => $currentOrganization->slug]) }}"
            style="display: inline-block; background: #2f65f6; color: white; padding: 11px 20px; border-radius: 7px; text-decoration: none; font-weight: 600;">
            + Tambah Vendor
        </a>
    </div>

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; overflow-x: auto;">

        @if ($vendors->isEmpty())
            <p style="color: #64748b; margin: 0;">
                Belum ada data vendor.
            </p>
        @else
            <table style="width: 100%; border-collapse: collapse; min-width: 850px;">
                <thead>
                    <tr style="background: #f1f5f9;">
                        <th style="padding: 12px; text-align: left;">No.</th>
                        <th style="padding: 12px; text-align: left;">Nama Vendor</th>
                        <th style="padding: 12px; text-align: left;">Nama Kontak</th>
                        <th style="padding: 12px; text-align: left;">Email</th>
                        <th style="padding: 12px; text-align: left;">Telepon</th>
                        <th style="padding: 12px; text-align: left;">Jenis Layanan</th>
                        <th style="padding: 12px; text-align: left;">Jumlah Penugasan</th>
                        <th style="padding: 12px; text-align: left;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($vendors as $index => $vendor)
                        <tr style="border-top: 1px solid #e2e8f0;">
                            <td style="padding: 12px;">{{ $index + 1 }}</td>
                            <td style="padding: 12px;">{{ $vendor->vendor_name }}</td>
                            <td style="padding: 12px;">{{ $vendor->contact_name ?: '—' }}</td>
                            <td style="padding: 12px;">{{ $vendor->email ?: '—' }}</td>
                            <td style="padding: 12px;">{{ $vendor->phone ?: '—' }}</td>
                            <td style="padding: 12px;">{{ $vendor->service_type }}</td>
                            <td style="padding: 12px;">{{ $vendor->event_assignments_count }}</td>
                            <td style="padding: 12px;">
                                <a href="{{ route('vendors.edit', [
                                    'organization' => $currentOrganization->slug,
                                    'vendor' => $vendor->id
                                ]) }}"
                                    style="color: #2563eb; text-decoration: underline;">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
</div>
@endsection