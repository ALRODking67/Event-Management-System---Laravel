@extends('layouts.ems')

@section('title', 'Tambah Peralatan')
@section('page-heading', 'Peralatan Teknis')

@section('content')
<style>
    .equipment-form-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 24px;
        max-width: 750px;
    }

    .equipment-form-title {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 24px;
    }

    .equipment-field {
        margin-bottom: 20px;
    }

    .equipment-field label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 7px;
    }

    .equipment-field input,
    .equipment-field select {
        display: block;
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: #fff;
        color: #1e293b;
        font-size: 13px;
        box-sizing: border-box;
    }

    .equipment-field input:focus,
    .equipment-field select:focus {
        outline: none;
        border-color: #2f65f6;
        box-shadow: 0 0 0 2px rgba(47, 101, 246, 0.12);
    }

    .equipment-error-box {
        background: #fee2e2;
        color: #991b1b;
        padding: 12px 16px;
        margin-bottom: 20px;
        border-radius: 6px;
        font-size: 13px;
    }

    .equipment-error-box ul {
        padding-left: 20px;
        margin: 0;
    }

    .equipment-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 24px;
    }

    .equipment-submit {
        display: inline-block;
        background: #2f65f6;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
    }

    .equipment-submit:hover {
        background: #2454d8;
    }

    .equipment-cancel {
        color: #475569;
        font-size: 13px;
        text-decoration: none;
    }

    .equipment-cancel:hover {
        color: #1e293b;
    }
</style>

<div class="equipment-form-card">

    <h3 class="equipment-form-title">
        Tambah Peralatan
    </h3>

    @if ($errors->any())
        <div class="equipment-error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('equipment.store') }}" method="POST">
        @csrf

        <div class="equipment-field">
            <label for="category_id">Kategori Peralatan</label>

            <select name="category_id" id="category_id" required>
                <option value="">-- Pilih Kategori --</option>

                @foreach ($categories as $category)
                    <option
                        value="{{ $category->id }}"
                        {{ old('category_id') == $category->id ? 'selected' : '' }}
                    >
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="equipment-field">
            <label for="equipment_name">Nama Peralatan</label>

            <input
                type="text"
                name="equipment_name"
                id="equipment_name"
                value="{{ old('equipment_name') }}"
                maxlength="150"
                required
                placeholder="Contoh: Mikrofon Wireless"
            >
        </div>

        <div class="equipment-field">
            <label for="asset_code">Kode Aset</label>

            <input
                type="text"
                name="asset_code"
                id="asset_code"
                value="{{ old('asset_code') }}"
                maxlength="80"
                required
                placeholder="Contoh: EQP-001"
            >
        </div>

        <div class="equipment-field">
            <label for="condition_status">Kondisi Peralatan</label>

            <select name="condition_status" id="condition_status" required>
                <option value="Good" {{ old('condition_status', 'Good') == 'Good' ? 'selected' : '' }}>
                    Baik
                </option>

                <option value="Minor Damage" {{ old('condition_status') == 'Minor Damage' ? 'selected' : '' }}>
                    Rusak Ringan
                </option>

                <option value="Major Damage" {{ old('condition_status') == 'Major Damage' ? 'selected' : '' }}>
                    Rusak Berat
                </option>

                <option value="Broken" {{ old('condition_status') == 'Broken' ? 'selected' : '' }}>
                    Rusak
                </option>
            </select>
        </div>

        <div class="equipment-field">
            <label for="availability_status">Status Ketersediaan</label>

            <select name="availability_status" id="availability_status" required>
                <option value="Available" {{ old('availability_status', 'Available') == 'Available' ? 'selected' : '' }}>
                    Tersedia
                </option>

                <option value="In Use" {{ old('availability_status') == 'In Use' ? 'selected' : '' }}>
                    Sedang Digunakan
                </option>

                <option value="Maintenance" {{ old('availability_status') == 'Maintenance' ? 'selected' : '' }}>
                    Dalam Pemeliharaan
                </option>

                <option value="Unavailable" {{ old('availability_status') == 'Unavailable' ? 'selected' : '' }}>
                    Tidak Tersedia
                </option>
            </select>
        </div>

        <div class="equipment-actions">
            <button type="submit" class="equipment-submit">
                Simpan Peralatan
            </button>

            <a href="{{ route('equipment.index') }}" class="equipment-cancel">
                Batal
            </a>
        </div>
    </form>

</div>
@endsection