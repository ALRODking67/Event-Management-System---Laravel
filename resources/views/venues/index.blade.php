@extends('layouts.ems')

@section('title', 'Kelola Venue')
@section('page-heading', 'Kelola Venue')

@section('content')
<style>
    .venue-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 24px;
    }

    .venue-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .venue-title {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
    }

    .venue-description {
        font-size: 13px;
        color: #64748b;
        margin-top: 5px;
    }

    .venue-button {
        display: inline-block;
        padding: 10px 16px;
        background: #2f65f6;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
    }

    .venue-button:hover {
        background: #2454d8;
    }

    .venue-success {
        padding: 12px 16px;
        margin-bottom: 20px;
        background: #dcfce7;
        color: #166534;
        border-radius: 6px;
        font-size: 13px;
    }

    .venue-empty {
        text-align: center;
        padding: 48px 16px;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
    }

    .venue-empty h4 {
        color: #334155;
        font-weight: 600;
    }

    .venue-empty p {
        color: #64748b;
        font-size: 13px;
        margin-top: 8px;
    }

    .venue-empty a {
        display: inline-block;
        margin-top: 16px;
        color: #2f65f6;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    .venue-table-wrapper {
        overflow-x: auto;
    }

    .venue-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .venue-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        padding: 13px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .venue-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 13px;
        color: #475569;
    }

    .venue-table td.venue-name {
        color: #1e293b;
        font-weight: 600;
    }

    .venue-edit {
        color: #2f65f6;
        font-weight: 600;
        text-decoration: none;
    }

    .venue-edit:hover {
        color: #1d4ed8;
    }
</style>

<div class="venue-card">

    <div class="venue-header">
        <div>
            <h3 class="venue-title">Daftar Venue</h3>
            <p class="venue-description">
                Kelola informasi lokasi penyelenggaraan acara.
            </p>
        </div>

        <a href="{{ route('venues.create', ['organization' => $currentOrganization->slug]) }}" class="venue-button">
            + Tambah Venue
        </a>
    </div>

    @if (session('success'))
        <div class="venue-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($venues->isEmpty())

        <div class="venue-empty">
            <h4>Belum ada data venue</h4>

            <p>
                Tambahkan venue untuk digunakan dalam penyelenggaraan acara.
            </p>

            <a href="{{ route('venues.create', ['organization' => $currentOrganization->slug]) }}">
                Tambah venue pertama
            </a>
        </div>

    @else

        <div class="venue-table-wrapper">
            <table class="venue-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Venue</th>
                        <th>Alamat</th>
                        <th>Kapasitas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($venues as $index => $venue)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td class="venue-name">
                                {{ $venue->venue_name }}
                            </td>

                            <td>
                                {{ $venue->address ?? '—' }}
                            </td>

                            <td>
                                {{ $venue->capacity ? number_format($venue->capacity) . ' orang' : '—' }}
                            </td>

                            <td>
                                <a
                                    href="{{ route('venues.edit', ['organization' => $currentOrganization->slug, 'venue' => $venue->id]) }}"
                                    class="venue-edit"
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