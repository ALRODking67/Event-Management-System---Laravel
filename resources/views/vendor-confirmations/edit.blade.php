@extends('layouts.ems')

@section('title', 'Edit Konfirmasi Vendor')
@section('page-heading', 'Edit Konfirmasi Vendor')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">

    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b;">
            Edit Konfirmasi Vendor
        </h2>

        <p style="color: #64748b; margin-top: 5px;">
            Perbarui status ketersediaan dan catatan vendor.
        </p>
    </div>

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 25px;">

        <form method="POST" action="{{ route('vendor-confirmations.update', $vendorConfirmation->id) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label for="event_vendor_id" style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Penugasan Vendor
                </label>

                <select name="event_vendor_id" id="event_vendor_id" required
                        style="width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <option value="">-- Pilih Penugasan Vendor --</option>

                    @foreach ($eventVendors as $eventVendor)
                        <option value="{{ $eventVendor->id }}"
                            {{ old('event_vendor_id', $vendorConfirmation->event_vendor_id) == $eventVendor->id ? 'selected' : '' }}>
                            {{ $eventVendor->event->event_name ?? 'Acara tidak ditemukan' }}
                            — {{ $eventVendor->vendor->vendor_name ?? 'Vendor tidak ditemukan' }}
                        </option>
                    @endforeach
                </select>

                @error('event_vendor_id')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="availability_status" style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Status Ketersediaan
                </label>

                <select name="availability_status" id="availability_status" required
                        style="width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <option value="Available"
                        {{ old('availability_status', $vendorConfirmation->availability_status) == 'Available' ? 'selected' : '' }}>
                        Tersedia
                    </option>

                    <option value="Unavailable"
                        {{ old('availability_status', $vendorConfirmation->availability_status) == 'Unavailable' ? 'selected' : '' }}>
                        Tidak Tersedia
                    </option>
                </select>

                @error('availability_status')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div style="margin-bottom: 25px;">
                <label for="notes" style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Catatan (Opsional)
                </label>

                <textarea name="notes" id="notes" rows="4"
                          style="width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px;">{{ old('notes', $vendorConfirmation->notes) }}</textarea>

                @error('notes')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div style="margin-bottom: 20px; color: #64748b; font-size: 14px;">
                Waktu konfirmasi terakhir:
                {{ $vendorConfirmation->confirmed_at
                    ? $vendorConfirmation->confirmed_at->format('d-m-Y H:i')
                    : 'Belum dikonfirmasi' }}
            </div>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="submit"
                        style="background: #2563eb; color: white; padding: 11px 20px; border: none; border-radius: 6px; cursor: pointer;">
                    Simpan Perubahan
                </button>

                <a href="{{ route('vendor-confirmations.index') }}"
                   style="background: #e2e8f0; color: #334155; padding: 11px 20px; border-radius: 6px; text-decoration: none;">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>
@endsection