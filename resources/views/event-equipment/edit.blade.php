
@extends('layouts.ems')

@section('title', 'Edit Penugasan Peralatan')
@section('page-heading', 'Edit Penugasan Peralatan')

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

        <form action="{{ route('event-equipment.update', [
            'organization' => $organization->slug,
            'eventEquipment' => $eventEquipment->id
        ]) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Acara
                </label>

                <select name="event_id" required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;">

                    @foreach ($events as $event)
                        <option value="{{ $event->id }}"
                            {{ old('event_id', $eventEquipment->event_id) == $event->id ? 'selected' : '' }}>
                            {{ $event->event_name }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Peralatan
                </label>

                <select name="equipment_id" required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;">

                    @foreach ($equipment as $item)
                        <option value="{{ $item->id }}"
                            {{ old('equipment_id', $eventEquipment->equipment_id) == $item->id ? 'selected' : '' }}>
                            {{ $item->equipment_name }} - {{ $item->category->category_name ?? '-' }} ({{ $item->asset_code }})
                        </option>
                    @endforeach

                </select>
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Jumlah Peralatan
                </label>

                <input type="number" name="quantity"
                    value="{{ old('quantity', $eventEquipment->quantity) }}"
                    min="1" required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;">
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Status Penggunaan
                </label>

                <select name="usage_status" required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;">

                    <option value="Planned"
                        {{ old('usage_status', $eventEquipment->usage_status) == 'Planned' ? 'selected' : '' }}>
                        Direncanakan
                    </option>

                    <option value="In Use"
                        {{ old('usage_status', $eventEquipment->usage_status) == 'In Use' ? 'selected' : '' }}>
                        Sedang Digunakan
                    </option>

                    <option value="Returned"
                        {{ old('usage_status', $eventEquipment->usage_status) == 'Returned' ? 'selected' : '' }}>
                        Sudah Dikembalikan
                    </option>

                </select>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Catatan Kondisi
                </label>

                <textarea name="condition_notes" rows="4"
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;">{{ old('condition_notes', $eventEquipment->condition_notes) }}</textarea>
            </div>

            <div style="display: flex; gap: 10px; align-items: center;">
                <button type="submit"
                    style="background: #2f65f6; color: white; padding: 11px 20px; border: none; border-radius: 7px; cursor: pointer; font-weight: 600;">
                    Simpan Perubahan
                </button>

                <a href="{{ route('event-equipment.index', ['organization' => $organization->slug]) }}"
                    style="color: #475569; text-decoration: none; padding: 11px 14px;">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
@endsection
