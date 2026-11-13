@extends('layouts.ems')

@section('title', 'Edit Venue')
@section('page-heading', 'Kelola Venue')

@section('content')
<style>
    .venue-form-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 24px;
        max-width: 850px;
    }

    .venue-form-title {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
    }

    .venue-form-description {
        font-size: 13px;
        color: #64748b;
        margin-top: 5px;
        margin-bottom: 24px;
    }

    .venue-field {
        margin-bottom: 20px;
    }

    .venue-field label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 7px;
    }

    .venue-required {
        color: #dc2626;
    }

    .venue-field input,
    .venue-field textarea {
        display: block;
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 13px;
        color: #1e293b;
        background: #fff;
        box-sizing: border-box;
    }

    .venue-field input:focus,
    .venue-field textarea:focus {
        outline: none;
        border-color: #2f65f6;
        box-shadow: 0 0 0 2px rgba(47, 101, 246, 0.12);
    }

    .venue-field textarea {
        resize: vertical;
    }

    .venue-error {
        color: #dc2626;
        font-size: 12px;
        margin-top: 5px;
    }

    .venue-form-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-top: 8px;
    }

    .venue-submit {
        display: inline-block;
        background: #2f65f6;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
    }

    .venue-submit:hover {
        background: #2454d8;
    }

    .venue-back {
        display: inline-block;
        padding: 10px 20px;
        background: #f1f5f9;
        color: #475569;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
    }

    .venue-back:hover {
        background: #e2e8f0;
    }
</style>

<div class="venue-form-card">

    <h3 class="venue-form-title">Edit Venue</h3>

    <p class="venue-form-description">
        Perbarui informasi venue yang sudah tersimpan.
    </p>

    <form action="{{ route('venues.update', ['organization' => $currentOrganization->slug, 'venue' => $venue->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="venue-field">
            <label for="venue_name">
                Nama Venue <span class="venue-required">*</span>
            </label>

            <input
                type="text"
                name="venue_name"
                id="venue_name"
                value="{{ old('venue_name', $venue->venue_name) }}"
                maxlength="150"
                required
            >

            @error('venue_name')
                <p class="venue-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="venue-field">
            <label for="address">Alamat</label>

            <textarea
                name="address"
                id="address"
                rows="3"
            >{{ old('address', $venue->address) }}</textarea>

            @error('address')
                <p class="venue-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="venue-field">
            <label for="capacity">Kapasitas Maksimum (Orang)</label>

            <input
                type="number"
                name="capacity"
                id="capacity"
                value="{{ old('capacity', $venue->capacity) }}"
                min="1"
                step="1"
            >

            @error('capacity')
                <p class="venue-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="venue-field">
            <label for="electricity_info">Informasi Kelistrikan</label>

            <textarea
                name="electricity_info"
                id="electricity_info"
                rows="3"
            >{{ old('electricity_info', $venue->electricity_info) }}</textarea>

            @error('electricity_info')
                <p class="venue-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="venue-field">
            <label for="access_info">Informasi Akses</label>

            <textarea
                name="access_info"
                id="access_info"
                rows="3"
            >{{ old('access_info', $venue->access_info) }}</textarea>

            @error('access_info')
                <p class="venue-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="venue-field">
            <label for="security_info">Informasi Keamanan</label>

            <textarea
                name="security_info"
                id="security_info"
                rows="3"
            >{{ old('security_info', $venue->security_info) }}</textarea>

            @error('security_info')
                <p class="venue-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="venue-field">
            <label for="parking_info">Informasi Parkir</label>

            <textarea
                name="parking_info"
                id="parking_info"
                rows="3"
            >{{ old('parking_info', $venue->parking_info) }}</textarea>

            @error('parking_info')
                <p class="venue-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="venue-form-actions">
            <button type="submit" class="venue-submit">
                Simpan Perubahan
            </button>

            <a href="{{ route('venues.index', ['organization' => $currentOrganization->slug]) }}" class="venue-back">
                Kembali
            </a>
        </div>

    </form>

</div>
@endsection