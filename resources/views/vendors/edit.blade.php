@extends('layouts.ems')

@section('title', 'Edit Vendor')
@section('page-heading', 'Edit Vendor')

@section('content')
<div style="max-width: 800px;">

    @if ($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 14px; margin-bottom: 20px; border-radius: 8px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px;">

        <form action="{{ route('vendors.update', [
            'organization' => $currentOrganization->slug,
            'vendor' => $vendor->id
        ]) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Nama Vendor
                </label>
                <input
                    type="text"
                    name="vendor_name"
                    value="{{ old('vendor_name', $vendor->vendor_name) }}"
                    maxlength="150"
                    required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;"
                >
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Nama Kontak
                </label>
                <input
                    type="text"
                    name="contact_name"
                    value="{{ old('contact_name', $vendor->contact_name) }}"
                    maxlength="100"
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;"
                >
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $vendor->email) }}"
                    maxlength="150"
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;"
                >
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Nomor Telepon
                </label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $vendor->phone) }}"
                    maxlength="30"
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;"
                >
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Jenis Layanan
                </label>
                <input
                    type="text"
                    name="service_type"
                    value="{{ old('service_type', $vendor->service_type) }}"
                    maxlength="100"
                    required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;"
                >
            </div>

            <div style="display: flex; gap: 10px; align-items: center;">
                <button
                    type="submit"
                    style="background: #2f65f6; color: white; padding: 11px 20px; border: none; border-radius: 7px; cursor: pointer; font-weight: 600;"
                >
                    Simpan Perubahan
                </button>

                <a
                    href="{{ route('vendors.index', ['organization' => $currentOrganization->slug]) }}"
                    style="color: #475569; text-decoration: none; padding: 11px 14px;"
                >
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
@endsection