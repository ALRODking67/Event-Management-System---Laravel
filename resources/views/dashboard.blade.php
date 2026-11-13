@extends('layouts.ems')

@section('title', 'Dashboard | EMS')
@section('page-heading', 'Dashboard')

@section('content')

<style>
    .dashboard-welcome {
        margin-bottom: 25px;
    }

    .dashboard-welcome h2 {
        font-size: 24px;
        font-weight: 750;
        margin: 0;
        color: #172033;
    }

    .ems-muted {
        color: #94A3B8;
        font-size: 13px;
    }

    .dashboard-welcome p {
        margin-top: 8px;
    }

    .dashboard-org-label {
        display: inline-block;
        margin-top: 12px;
        padding: 7px 12px;
        border-radius: 7px;
        background: #EFF6FF;
        color: #2563EB;
        font-size: 12px;
        font-weight: 600;
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
        font-weight: 600;
    }

    .ems-kpi-value {
        font-size: 29px;
        font-weight: 750;
        margin-top: 14px;
        color: #172033;
    }

    .ems-kpi-description {
        color: #94A3B8;
        font-size: 12px;
        margin-top: 8px;
    }

    .ems-content-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 20px;
    }

    .dashboard-section-title {
        font-size: 17px;
        font-weight: 700;
        margin: 0;
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
        white-space: nowrap;
    }

    .ems-empty {
        padding: 40px 10px;
        text-align: center;
    }

    .dashboard-info-list {
        margin-top: 22px;
    }

    .dashboard-info-item {
        padding: 14px 0;
        border-bottom: 1px solid #F1F5F9;
    }

    .dashboard-info-item:last-child {
        border-bottom: 0;
    }

    .dashboard-info-item strong {
        display: block;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .dashboard-info-item span {
        color: #94A3B8;
        font-size: 12px;
    }

    .finance-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
    }
    .finance-card-label {
        color: #64748b;
        font-size: 13px;
        margin-bottom: 10px;
    }
    .finance-card-value {
        font-size: 23px;
        font-weight: 700;
        color: #0f172a;
    }
    .finance-card.income .finance-card-value { color: #059669; }
    .finance-card.expense .finance-card-value { color: #dc2626; }
    .finance-card.balance .finance-card-value {
        color: {{ $netBalance >= 0 ? '#059669' : '#dc2626' }};
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
    <h2>Selamat datang, {{ Auth::user()->name }}!</h2>

    <p class="ems-muted">
        Berikut ringkasan aktivitas organisasi Anda di Event Management System.
    </p>

    <div class="dashboard-org-label">
        {{ $organization->name }} | {{ $roleName }}
    </div>
</section>


{{-- ========================================= --}}
{{-- DASHBOARD VENDOR --}}
{{-- ========================================= --}}

@if ($isVendor)

    <section class="ems-kpi-grid">

        <div class="ems-card">
            <div class="ems-kpi-label">Acara Ditugaskan</div>

            <div class="ems-kpi-value">
                {{ $totalEvents }}
            </div>

            <div class="ems-kpi-description">
                Jumlah acara berbeda yang melibatkan Anda
            </div>
        </div>

        <div class="ems-card">
            <div class="ems-kpi-label">Konfirmasi Terjawab</div>

            <div class="ems-kpi-value">
                {{ $vendorConfirmationResponses }}
            </div>

            <div class="ems-kpi-description">
                Penugasan yang sudah mendapatkan konfirmasi
            </div>
        </div>

        <div class="ems-card">
            <div class="ems-kpi-label">Menunggu Konfirmasi</div>

            <div class="ems-kpi-value">
                {{ $pendingVendorConfirmations }}
            </div>

            <div class="ems-kpi-description">
                Penugasan yang belum dikonfirmasi
            </div>
        </div>

        <div class="ems-card">
            <div class="ems-kpi-label">Jadwal Mendatang</div>

            <div class="ems-kpi-value">
                {{ $upcomingEvents->count() }}
            </div>

            <div class="ems-kpi-description">
                Acara mendatang yang melibatkan Anda
            </div>
        </div>

    </section>

@else

    {{-- ========================================= --}}
    {{-- DASHBOARD ADMIN DAN STAF --}}
    {{-- ========================================= --}}

    <section class="ems-kpi-grid">

        <div class="ems-card">
            <div class="ems-kpi-label">Total Acara</div>

            <div class="ems-kpi-value">
                {{ $totalEvents }}
            </div>

            <div class="ems-kpi-description">
                Total acara dalam organisasi
            </div>
        </div>

        <div class="ems-card">
            <div class="ems-kpi-label">Respons Konfirmasi Vendor</div>

            <div class="ems-kpi-value">
                {{ $vendorConfirmationResponses }}
            </div>

            <div class="ems-kpi-description">
                Penugasan vendor yang sudah dijawab
            </div>
        </div>

        <div class="ems-card">
            <div class="ems-kpi-label">Peralatan Siap</div>

            <div class="ems-kpi-value">
                {{ $equipmentReadyPercentage !== null
                    ? $equipmentReadyPercentage . '%'
                    : '�' }}
            </div>

            <div class="ems-kpi-description">
                {{ $readyEquipment }} dari {{ $totalEquipment }} peralatan
            </div>
        </div>

        @if ($isAdmin)

            <div class="ems-card">
                <div class="ems-kpi-label">Estimasi ROI</div>

                <div class="ems-kpi-value">
                    {{ $estimatedRoi !== null
                        ? $estimatedRoi . '%'
                        : '�' }}
                </div>

                <div class="ems-kpi-description">
                    Perbandingan pemasukan dan pengeluaran
                </div>
            </div>

        @else

            <div class="ems-card">
                <div class="ems-kpi-label">Menunggu Konfirmasi Vendor</div>

                <div class="ems-kpi-value">
                    {{ $pendingVendorConfirmations }}
                </div>

                <div class="ems-kpi-description">
                    Penugasan vendor yang belum dijawab
                </div>
            </div>

        @endif

    </section>

@endif

@if (!$isVendor)
    <div style="margin-top:28px;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:15px;flex-wrap:wrap;">
            <div>
                <h3 style="font-size:18px;font-weight:700;margin:0;">
                    Ringkasan Keuangan
                </h3>
                <p style="font-size:12px;color:#64748b;margin:5px 0 0;">
                    Rekapitulasi transaksi organisasi
                </p>
            </div>

            <a href="{{ route('financial-transactions.index', ['organization' => $organization->slug]) }}"
               style="font-size:13px;color:#2563eb;text-decoration:none;font-weight:600;">
                Kelola Transaksi →
            </a>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:15px;">
            <div class="finance-card income">
                <div class="finance-card-label">Total Pemasukan</div>
                <div class="finance-card-value">
                    Rp{{ number_format((float) $totalIncome, 0, ',', '.') }}
                </div>
            </div>

            <div class="finance-card expense">
                <div class="finance-card-label">Total Pengeluaran</div>
                <div class="finance-card-value">
                    Rp{{ number_format((float) $totalExpense, 0, ',', '.') }}
                </div>
            </div>

            <div class="finance-card balance">
                <div class="finance-card-label">Selisih Keuangan</div>
                <div class="finance-card-value">
                    Rp{{ number_format((float) $netBalance, 0, ',', '.') }}
                </div>
            </div>

            <div class="finance-card">
                <div class="finance-card-label">Estimasi ROI</div>
                <div class="finance-card-value">
                    {{ $estimatedRoi !== null ? $estimatedRoi . '%' : '—' }}
                </div>
            </div>
        </div>
    </div>
@endif

{{-- ========================================= --}}
{{-- JADWAL ACARA --}}
{{-- ========================================= --}}

<section class="ems-content-grid">

    <div class="ems-card">

        <h3 class="dashboard-section-title">
            {{ $isVendor ? 'Jadwal Penugasan Mendatang' : 'Jadwal Acara Mendatang' }}
        </h3>

        <p class="ems-muted" style="margin-top:5px;">
            {{ $isVendor
                ? 'Daftar acara mendatang yang melibatkan Anda sebagai vendor.'
                : 'Pantau jadwal acara dalam organisasi Anda.' }}
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
                                    {{ $event->start_datetime?->format('d M Y, H:i') ?? '-' }}
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

                <p style="font-weight:600;margin-top:12px;">
                    Belum ada acara mendatang
                </p>

                <p class="ems-muted" style="margin-top:5px;">
                    {{ $isVendor
                        ? 'Penugasan acara mendatang akan muncul di sini.'
                        : 'Acara mendatang organisasi akan muncul di sini.' }}
                </p>

            </div>

        @endif

    </div>


    {{-- ========================================= --}}
    {{-- INFORMASI DASHBOARD --}}
    {{-- ========================================= --}}

    <div class="ems-card">

        <h3 class="dashboard-section-title">
            Informasi Organisasi
        </h3>

        <p class="ems-muted" style="margin-top:5px;">
            Ringkasan akses dan informasi akun Anda.
        </p>

        <div class="dashboard-info-list">

            <div class="dashboard-info-item">
                <strong>Nama Organisasi</strong>
                <span>{{ $organization->name }}</span>
            </div>

            <div class="dashboard-info-item">
                <strong>Role Keanggotaan</strong>
                <span>{{ $roleName }}</span>
            </div>

            <div class="dashboard-info-item">
                <strong>Nama Pengguna</strong>
                <span>{{ Auth::user()->name }}</span>
            </div>

            <div class="dashboard-info-item">
                <strong>Email</strong>
                <span>{{ Auth::user()->email }}</span>
            </div>

            <div class="dashboard-info-item">
                <strong>Waktu Pembaruan</strong>
                <span>{{ now()->format('d M Y, H:i') }}</span>
            </div>

        </div>

    </div>

</section>


<footer class="ems-muted"
    style="display:flex;justify-content:space-between;margin-top:35px;flex-wrap:wrap;gap:10px;">

    <span>EMS - Event Management System</span>

    <span>Data diperbarui {{ now()->format('d M Y, H:i') }}</span>

</footer>

@endsection
