@extends('layouts.ems')

@section('title', 'Edit Penugasan Vendor')
@section('page-heading', 'Edit Penugasan Vendor')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">

    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b;">
            Edit Penugasan Vendor
        </h2>
        <p style="color: #64748b; margin-top: 5px;">
            Perbarui informasi penugasan vendor untuk acara.
        </p>
    </div>

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 25px;">

        <form method="POST" action="{{ route('event-vendors.update', $eventVendor->id) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label for="event_id" style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Acara
                </label>

                <select name="event_id" id="event_id" required
                        style="width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <option value="">-- Pilih Acara --</option>

                    @foreach ($events as $event)
                        <option value="{{ $event->id }}"
                            {{ old('event_id', $eventVendor->event_id) == $event->id ? 'selected' : '' }}>
                            {{ $event->event_name }}
                        </option>
                    @endforeach
                </select>

                @error('event_id')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="vendor_id" style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Vendor
                </label>

                <select name="vendor_id" id="vendor_id" required
                        style="width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <option value="">-- Pilih Vendor --</option>

                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}"
                            {{ old('vendor_id', $eventVendor->vendor_id) == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->vendor_name }} - {{ $vendor->service_type }}
                        </option>
                    @endforeach
                </select>

                @error('vendor_id')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="service_description" style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Deskripsi Layanan
                </label>

                <textarea name="service_description" id="service_description" rows="4" required
                          style="width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px;">{{ old('service_description', $eventVendor->service_description) }}</textarea>

                @error('service_description')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="agreed_cost" style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Biaya Kesepakatan (Rp)
                </label>

                <input type="number" name="agreed_cost" id="agreed_cost"
                       min="0" step="0.01" required
                       value="{{ old('agreed_cost', $eventVendor->agreed_cost) }}"
                       style="width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px;">

                @error('agreed_cost')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 25px;">
                <label for="assignment_status" style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Status Penugasan
                </label>

                <select name="assignment_status" id="assignment_status" required
                        style="width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <option value="Requested" {{ old('assignment_status', $eventVendor->assignment_status) == 'Requested' ? 'selected' : '' }}>
                        Requested
                    </option>

                    <option value="Confirmed" {{ old('assignment_status', $eventVendor->assignment_status) == 'Confirmed' ? 'selected' : '' }}>
                        Confirmed
                    </option>

                    <option value="In Progress" {{ old('assignment_status', $eventVendor->assignment_status) == 'In Progress' ? 'selected' : '' }}>
                        In Progress
                    </option>

                    <option value="Completed" {{ old('assignment_status', $eventVendor->assignment_status) == 'Completed' ? 'selected' : '' }}>
                        Completed
                    </option>
                </select>

                @error('assignment_status')
                    <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="submit"
                        style="background: #2563eb; color: white; padding: 11px 20px; border: none; border-radius: 6px; cursor: pointer;">
                    Simpan Perubahan
                </button>

                <a href="{{ route('event-vendors.index') }}"
                   style="background: #e2e8f0; color: #334155; padding: 11px 20px; border-radius: 6px; text-decoration: none;">
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>
@endsection