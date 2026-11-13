@extends('layouts.ems')

@section('title', 'Tambah Penugasan Vendor')
@section('page-heading', 'Tambah Penugasan Vendor')

@section('content')
<div style="max-width:850px;">
    <p style="color:#64748b; margin-bottom:20px;">
        Hubungkan vendor dengan acara dan tentukan layanan serta biaya kesepakatannya.
    </p>

    <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; padding:25px;">
        <form method="POST" action="{{ route('event-vendors.store', ['organization' => $organization->slug]) }}">
            @csrf

            <div style="margin-bottom:20px;">
                <label for="event_id" style="display:block; font-weight:600; margin-bottom:7px;">
                    Acara
                </label>

                <select name="event_id" id="event_id" required
                        style="width:100%; padding:11px; border:1px solid #cbd5e1; border-radius:7px;">
                    <option value="">-- Pilih Acara --</option>
                    @foreach ($events as $event)
                        <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->event_name }}
                        </option>
                    @endforeach
                </select>

                @error('event_id')
                    <p style="color:#dc2626; font-size:13px; margin-top:5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom:20px;">
                <label for="vendor_id" style="display:block; font-weight:600; margin-bottom:7px;">
                    Vendor
                </label>

                <select name="vendor_id" id="vendor_id" required
                        style="width:100%; padding:11px; border:1px solid #cbd5e1; border-radius:7px;">
                    <option value="">-- Pilih Vendor --</option>
                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->vendor_name }} - {{ $vendor->service_type }}
                        </option>
                    @endforeach
                </select>

                @error('vendor_id')
                    <p style="color:#dc2626; font-size:13px; margin-top:5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom:20px;">
                <label for="service_description" style="display:block; font-weight:600; margin-bottom:7px;">
                    Deskripsi Layanan
                </label>

                <textarea name="service_description" id="service_description" rows="4" required
                          style="width:100%; padding:11px; border:1px solid #cbd5e1; border-radius:7px;">{{ old('service_description') }}</textarea>

                @error('service_description')
                    <p style="color:#dc2626; font-size:13px; margin-top:5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom:25px;">
                <label for="agreed_cost" style="display:block; font-weight:600; margin-bottom:7px;">
                    Biaya Kesepakatan (Rp)
                </label>

                <input type="number" name="agreed_cost" id="agreed_cost"
                       min="0" step="0.01" required
                       value="{{ old('agreed_cost', 0) }}"
                       style="width:100%; padding:11px; border:1px solid #cbd5e1; border-radius:7px;">

                @error('agreed_cost')
                    <p style="color:#dc2626; font-size:13px; margin-top:5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="background:#eff6ff; color:#1e40af; padding:13px 15px; border-radius:7px; margin-bottom:22px; font-size:14px;">
                Status awal penugasan akan otomatis menjadi <strong>Requested</strong>. Vendor perlu memberikan konfirmasi terlebih dahulu.
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="submit"
                        style="background:#2f65f6; color:white; padding:11px 20px; border:none; border-radius:7px; cursor:pointer; font-weight:600;">
                    Simpan Penugasan
                </button>

                <a href="{{ route('vendor-confirmations.index', ['organization' => $organization->slug]) }}"
                style="background:#e2e8f0; color:#334155; padding:11px 20px; border-radius:7px; text-decoration:none;">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
