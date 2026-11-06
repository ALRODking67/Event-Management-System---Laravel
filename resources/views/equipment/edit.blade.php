@extends('layouts.ems')

@section('title', 'Edit Peralatan')

@section('page-heading', 'Edit Peralatan')

@section('content')
<div style="max-width: 760px;">

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;">

        <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
            Edit Peralatan
        </h2>

        <p style="font-size: 14px; color: #64748b; margin-bottom: 24px;">
            Perbarui informasi peralatan yang sudah terdaftar.
        </p>

        @if ($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px;">
                <strong>Periksa kembali data berikut:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('equipment.update', $equipment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 18px;">
                <label for="category_id" style="display: block; font-weight: 600; margin-bottom: 7px; color: #334155;">
                    Kategori Peralatan
                </label>

                <select
                    id="category_id"
                    name="category_id"
                    required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px; background: white;"
                >
                    @foreach ($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            {{ old('category_id', $equipment->category_id) == $category->id ? 'selected' : '' }}
                        >
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 18px;">
                <label for="equipment_name" style="display: block; font-weight: 600; margin-bottom: 7px; color: #334155;">
                    Nama Peralatan
                </label>

                <input
                    id="equipment_name"
                    type="text"
                    name="equipment_name"
                    value="{{ old('equipment_name', $equipment->equipment_name) }}"
                    maxlength="150"
                    required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px; box-sizing: border-box;"
                >
            </div>

            <div style="margin-bottom: 18px;">
                <label for="asset_code" style="display: block; font-weight: 600; margin-bottom: 7px; color: #334155;">
                    Kode Aset
                </label>

                <input
                    id="asset_code"
                    type="text"
                    name="asset_code"
                    value="{{ old('asset_code', $equipment->asset_code) }}"
                    maxlength="80"
                    required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px; box-sizing: border-box;"
                >
            </div>

            <div style="margin-bottom: 18px;">
                <label for="condition_status" style="display: block; font-weight: 600; margin-bottom: 7px; color: #334155;">
                    Kondisi Peralatan
                </label>

                <select
                    id="condition_status"
                    name="condition_status"
                    required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px; background: white;"
                >
                    <option value="Good" {{ old('condition_status', $equipment->condition_status) == 'Good' ? 'selected' : '' }}>
                        Baik
                    </option>

                    <option value="Minor Damage" {{ old('condition_status', $equipment->condition_status) == 'Minor Damage' ? 'selected' : '' }}>
                        Rusak Ringan
                    </option>

                    <option value="Major Damage" {{ old('condition_status', $equipment->condition_status) == 'Major Damage' ? 'selected' : '' }}>
                        Rusak Berat
                    </option>

                    <option value="Broken" {{ old('condition_status', $equipment->condition_status) == 'Broken' ? 'selected' : '' }}>
                        Rusak
                    </option>
                </select>
            </div>

            <div style="margin-bottom: 24px;">
                <label for="availability_status" style="display: block; font-weight: 600; margin-bottom: 7px; color: #334155;">
                    Status Ketersediaan
                </label>

                <select
                    id="availability_status"
                    name="availability_status"
                    required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px; background: white;"
                >
                    <option value="Available" {{ old('availability_status', $equipment->availability_status) == 'Available' ? 'selected' : '' }}>
                        Tersedia
                    </option>

                    <option value="In Use" {{ old('availability_status', $equipment->availability_status) == 'In Use' ? 'selected' : '' }}>
                        Sedang Digunakan
                    </option>

                    <option value="Maintenance" {{ old('availability_status', $equipment->availability_status) == 'Maintenance' ? 'selected' : '' }}>
                        Dalam Pemeliharaan
                    </option>

                    <option value="Unavailable" {{ old('availability_status', $equipment->availability_status) == 'Unavailable' ? 'selected' : '' }}>
                        Tidak Tersedia
                    </option>
                </select>
            </div>

            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <button
                    type="submit"
                    style="background: #2f65f6; color: white; border: none; padding: 11px 20px; border-radius: 7px; font-weight: 600; cursor: pointer;"
                >
                    Simpan Perubahan
                </button>

                <a
                    href="{{ route('equipment.index') }}"
                    style="display: inline-block; color: #475569; text-decoration: none; padding: 11px 16px; border: 1px solid #cbd5e1; border-radius: 7px;"
                >
                    Batal
                </a>
            </div>
        </form>

    </div>

</div>
@endsection