
@extends('layouts.ems')

@section('title', 'Dashboard | EMS')
@section('page-heading', 'Dashboard')

@section('content')

<style>
    .dashboard-welcome {
        margin-bottom: 25px;
    }

    .dashboard-welcome h2 {
        font-size: 25px;
        font-weight: 750;
        margin: 0;
    }

    .dashboard-welcome p {
        margin-top: 8px;
    }

    .ems-muted {
        color: #94A3B8;
        font-size: 13px;
    }

    .ems-card {
        background: white;
        border: 1px solid #E2E8F0;
        border-radius: 13px;
        padding: 22px;
        min-width: 0;
        box-sizing: border-box;
    }

    .ems-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin: 25px 0;
    }

    .ems-kpi-label {
        color: #64748B;
        font-size: 13px;
    }

    .ems-kpi-value {
        font-size: 29px;
        font-weight: 750;
        margin-top: 14px;
        color: #172033;
    }

    .ems-content-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 20px;
    }

    .ems-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 22px;
        font-size: 13px;
    }

    .ems-table th {
        text-align: left;
        color: #64748B;
        font-weight: 600;
        padding: 12px 10px;
        border-bottom: 1px solid #E2E8F0;
        white-space: nowrap;
    }

    .ems-table td {
        padding: 14px 10px;
        border-bottom: 1px solid #F1F5F9;
    }

    .ems-status {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 20px;
        background: #EFF6FF;
        color: #2563EB;
        font-size: 11px;
    }

    .ems-empty {
        padding: 45px 10px;
        text-align: center;
    }

    .dashboard-section-title {
        font-size: 17px;
        font-weight: 700;
        margin: 0;
    }

    @media (max-width: 1100px) {
        .ems-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ems-content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .ems-kpi-grid {
            grid-template-columns: 1fr;
        }

        .ems-card {
            overflow-x: auto;
        }

        .dashboard-welcome h2 {
            font-size: 21px;
        }
    }
</style>

<section class="dashboard-welcome">
    <h2>Selamat datang, {{ Auth::user()->name }}! 👋</h2>

    <p class="ems-muted">
        Berikut ringkasan aktivitas Event Management System.
    </p>
</section>

{{-- KPI DASHBOARD --}}
<section class="ems-kpi-grid">

    <div class="ems-card">
        <div class="ems-kpi-label">Total Acara</div>

        <div class="ems-kpi-value">
            {{ $totalEvents }}
        </div>

        <div class="ems-muted">
            Total acara terdaftar
        </div>
    </div>

    <div class="ems-card">
        <div class="ems-kpi-label">Respons Konfirmasi Vendor</div>

        <div class="ems-kpi-value">
            {{ $vendorConfirmationResponses }}
        </div>

        <div class="ems-muted">
            Jumlah penugasan vendor yang sudah dijawab
        </div>
    </div>

    <div class="ems-card">
        <div class="ems-kpi-label">Peralatan Siap</div>

        <div class="ems-kpi-value">
            {{ $equipmentReadyPercentage !== null
                ? $equipmentReadyPercentage . '%'
                : '—' }}
        </div>

        <div class="ems-muted">
            {{ $readyEquipment }} dari {{ $totalEquipment }} peralatan
        </div>
    </div>

    <div class="ems-card">
        <div class="ems-kpi-label">Estimasi ROI</div>

        <div class="ems-kpi-value">
            {{ $estimatedRoi !== null
                ? $estimatedRoi . '%'
                : '—' }}
        </div>

        <div class="ems-muted">
            Belum ada perhitungan transaksi
        </div>
    </div>

</section>

{{-- KONTEN DASHBOARD --}}
<section class="ems-content-grid">

    <div class="ems-card">
        <h3 class="dashboard-section-title">
            Jadwal Acara Mendatang
        </h3>

        <p class="ems-muted" style="margin-top:5px;">
            Pantau jadwal dan kesiapan setiap acara.
        </p>

        @if ($upcomingEvents->isNotEmpty())

            <div style="overflow-x:auto;">
                <table class="ems-table">
                    <thead>
                        <tr>
                            <th>Nama Acara</th>
                            <th>Tanggal</th>
                            <th>Venue</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($upcomingEvents as $event)
                            <tr>
                                <td>
                                    <strong>{{ $event->event_name }}</strong>
                                </td>

                                <td>
                                    {{ $event->start_datetime->format('d M Y') }}
                                </td>

                                <td>
                                    {{ $event->venue?->venue_name ?? 'Belum ditentukan' }}
                                </td>

                                <td>
                                    <span class="ems-status">
                                        {{ $event->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @else

            <div class="ems-empty">
                <div style="font-size:30px;">▦</div>

                <p style="font-weight:600;margin-top:12px;">
                    Belum ada acara mendatang
                </p>

                <p class="ems-muted" style="margin-top:5px;">
                    Acara yang sudah terdaftar akan muncul di sini.
                </p>
            </div>

        @endif
    </div>

    <div class="ems-card">
        <h3 class="dashboard-section-title">
            Aktivitas Terbaru
        </h3>

        <p class="ems-muted" style="margin-top:5px;">
            Ringkasan aktivitas dalam sistem.
        </p>

        <div class="ems-empty">
            <div style="font-size:30px;">◷</div>

            <p style="font-weight:600;margin-top:12px;">
                Belum ada aktivitas
            </p>

            <p class="ems-muted" style="margin-top:5px;">
                Aktivitas akan ditampilkan setelah fitur terhubung.
            </p>
        </div>
    </div>

</section>

<footer class="ems-muted"
    style="display:flex;justify-content:space-between;margin-top:35px;flex-wrap:wrap;gap:10px;">
    <span>EMS • Event Management System</span>
    <span>Data diperbarui {{ now()->format('d M Y, H:i') }}</span>
</footer>

@endsection
