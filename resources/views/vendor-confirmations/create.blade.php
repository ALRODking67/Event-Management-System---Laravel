@extends('layouts.ems')

@section('title', 'Jawab Permintaan Vendor')
@section('page-heading', 'Jawab Permintaan Vendor')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">

    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b;">
            Jawab Permintaan Ketersediaan
        </h2>

        <p style="color: #64748b; margin-top: 5px;">
            Periksa detail penugasan, lalu berikan jawaban ketersediaan layanan Anda.
        </p>
    </div>

    @if ($eventVendors->isEmpty())
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 25px;">
            <h3 style="font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Tidak Ada Permintaan yang Menunggu Jawaban
            </h3>

            <p style="color: #64748b; margin-bottom: 20px;">
                Saat ini tidak ada penugasan berstatus Requested yang menunggu jawaban dari akun Vendor Anda.
            </p>

            <a href="{{ route('vendor-confirmations.index', ['organization' => $organization->slug]) }}"
               style="background: #e2e8f0; color: #334155; padding: 11px 20px; border-radius: 6px; text-decoration: none;">
                Kembali ke Daftar Konfirmasi
            </a>
        </div>
    @else
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 25px;">

            @if ($errors->any())
                <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 7px; margin-bottom: 20px;">
                    Periksa kembali data yang kamu masukkan.
                </div>
            @endif

            <form method="POST"
                  action="{{ route('vendor-confirmations.store', ['organization' => $organization->slug]) }}">
                @csrf

                <div style="margin-bottom: 20px;">
                    <label for="event_vendor_id" style="display: block; font-weight: 600; margin-bottom: 7px;">
                        Penugasan yang Dijawab
                    </label>

                    <select name="event_vendor_id" id="event_vendor_id" required
                            style="width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <option value="">-- Pilih Penugasan --</option>

                        @foreach ($eventVendors as $eventVendor)
                            <option value="{{ $eventVendor->id }}"
                                {{ old('event_vendor_id') == $eventVendor->id ? 'selected' : '' }}>
                                {{ $eventVendor->event->event_name ?? 'Acara tidak ditemukan' }}
                                — {{ $eventVendor->service_description }}
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
                        Jawaban Ketersediaan
                    </label>

                    <select name="availability_status" id="availability_status" required
                            style="width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <option value="">-- Pilih Jawaban --</option>
                        <option value="Available" {{ old('availability_status') === 'Available' ? 'selected' : '' }}>
                            Tersedia
                        </option>
                        <option value="Unavailable" {{ old('availability_status') === 'Unavailable' ? 'selected' : '' }}>
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
                              style="width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px;"
                              placeholder="Contoh: Layanan tersedia, tetapi perlu penyesuaian waktu pemasangan.">{{ old('notes') }}</textarea>

                    @error('notes')
                        <p style="color: #dc2626; font-size: 13px; margin-top: 5px;">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="submit"
                            style="background: #2563eb; color: white; padding: 11px 20px; border: none; border-radius: 6px; cursor: pointer;">
                        Kirim Jawaban
                    </button>

                    <a href="{{ route('vendor-confirmations.index', ['organization' => $organization->slug]) }}"
                       style="background: #e2e8f0; color: #334155; padding: 11px 20px; border-radius: 6px; text-decoration: none;">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
