@extends('layouts.ems')

@section('title', 'Inventaris Peralatan')
@section('page-heading', 'Peralatan Teknis')

@section('content')
<style>
    .equipment-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 24px;
    }

    .equipment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .equipment-title {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
    }

    .equipment-description {
        font-size: 13px;
        color: #64748b;
        margin-top: 5px;
    }

    .equipment-add-button {
        display: inline-block;
        padding: 10px 16px;
        background: #2f65f6;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
    }

    .equipment-add-button:hover {
        background: #2454d8;
    }

    .equipment-success {
        background: #dcfce7;
        color: #166534;
        padding: 12px 16px;
        margin-bottom: 20px;
        border-radius: 6px;
        font-size: 13px;
    }

    .equipment-table-wrapper {
        overflow-x: auto;
    }

    .equipment-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .equipment-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        padding: 13px 14px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .equipment-table td {
        padding: 14px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 13px;
        color: #475569;
    }

    .equipment-table td.equipment-name {
        color: #1e293b;
        font-weight: 600;
    }

    .equipment-edit {
        color: #2f65f6;
        font-weight: 600;
        text-decoration: none;
    }

    .equipment-edit:hover {
        color: #1d4ed8;
    }

    .equipment-empty {
        text-align: center;
        padding: 40px 16px;
        color: #64748b;
        font-size: 13px;
    }
</style>

<div class="equipment-card">

    <div class="equipment-header">
        <div>
            <h3 class="equipment-title">
                Inventaris Peralatan Teknis
            </h3>

            <p class="equipment-description">
                Kelola data peralatan dan kondisi inventaris yang tersedia.
            </p>
        </div>

        <a href="{{ route('equipment.create', ['organization' => $currentOrganization->slug]) }}" class="equipment-add-button">
            + Tambah Peralatan
        </a>
    </div>

    @if (session('success'))
        <div class="equipment-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($equipment->isEmpty())

        <div class="equipment-empty">
            Belum ada data peralatan dalam inventaris.
        </div>

    @else

        <div class="equipment-table-wrapper">
            <table class="equipment-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode Aset</th>
                        <th>Nama Peralatan</th>
                        <th>Kategori</th>
                        <th>Kondisi</th>
                        <th>Ketersediaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($equipment as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>{{ $item->asset_code }}</td>

                            <td class="equipment-name">
                                {{ $item->equipment_name }}
                            </td>

                            <td>
                                {{ $item->category->category_name }}
                            </td>

                            <td>
                                {{ $item->condition_status }}
                            </td>

                            <td>
                                {{ $item->availability_status }}
                            </td>

                            <td>
                                <a
                                    href="{{ route('equipment.edit', ['organization' => $currentOrganization->slug, 'equipment' => $item->id]) }}"
                                    class="equipment-edit"
                                >
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

</div>
@endsection