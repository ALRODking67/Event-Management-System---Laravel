@extends('layouts.ems')

@section('title', 'Laporan Evaluasi')
@section('page-heading', 'Laporan Evaluasi Acara')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:25px;">
        <div>
            <h2 style="font-size:24px; font-weight:700; color:#111C2E;">
                Laporan Evaluasi Acara
            </h2>

            <p style="color:#64748b; margin-top:5px;">
                Ringkasan pelaksanaan acara, keuangan, dan evaluasi.
            </p>
        </div>

        <button
            type="button"
            onclick="window.print()"
            style="background:#2F65F6; color:white; padding:10px 18px; border:0; border-radius:6px; cursor:pointer;"
        >
            Cetak Laporan
        </button>
    </div>

    @forelse ($events as $event)

        @php
            $income = $event->financialTransactions
                ->where('transaction_type', 'Income')
                ->sum('amount');

            $expense = $event->financialTransactions
                ->where('transaction_type', 'Expense')
                ->sum('amount');

            $balance = $income - $expense;

            $roi = $expense > 0
                ? round(($balance / $expense) * 100, 1)
                : null;

            $publishedRundowns = $event->rundownItems
                ->where('is_published', true)
                ->count();

            $totalRundowns = $event->rundownItems->count();

            $vendorCount = $event->vendorAssignments->count();

            $equipmentCount = $event->equipmentAssignments->count();
        @endphp

        <div class="report-card" style="background:white; border:1px solid #E2E8F0; border-radius:10px; padding:24px; margin-bottom:25px; page-break-inside:avoid;">

            {{-- INFORMASI ACARA --}}
            <div style="border-bottom:1px solid #E2E8F0; padding-bottom:18px; margin-bottom:20px;">

                <h3 style="font-size:22px; font-weight:bold; color:#111C2E;">
                    {{ $event->event_name }}
                </h3>

                <p style="margin-top:8px;">
                    <strong>Tanggal:</strong>
                    {{ $event->start_datetime
                        ? $event->start_datetime->format('d-m-Y H:i')
                        : '—' }}
                </p>

                <p>
                    <strong>Venue:</strong>
                    {{ $event->venue->venue_name ?? 'Belum ditentukan' }}
                </p>

                <p>
                    <strong>Status acara:</strong>
                    {{ $event->status }}
                </p>
            </div>

            {{-- RINGKASAN KEUANGAN --}}
            <h4 style="font-size:18px; font-weight:bold; margin-bottom:12px;">
                Ringkasan Keuangan
            </h4>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-bottom:25px;">

                <div style="background:#F8FAFC; border:1px solid #E2E8F0; padding:15px; border-radius:8px;">
                    <p style="color:#64748b; font-size:13px;">Total Pemasukan</p>
                    <p style="font-size:20px; font-weight:bold; color:#059669;">
                        Rp {{ number_format($income, 0, ',', '.') }}
                    </p>
                </div>

                <div style="background:#F8FAFC; border:1px solid #E2E8F0; padding:15px; border-radius:8px;">
                    <p style="color:#64748b; font-size:13px;">Total Pengeluaran</p>
                    <p style="font-size:20px; font-weight:bold; color:#dc2626;">
                        Rp {{ number_format($expense, 0, ',', '.') }}
                    </p>
                </div>

                <div style="background:#F8FAFC; border:1px solid #E2E8F0; padding:15px; border-radius:8px;">
                    <p style="color:#64748b; font-size:13px;">Saldo</p>
                    <p style="font-size:20px; font-weight:bold;">
                        Rp {{ number_format($balance, 0, ',', '.') }}
                    </p>
                </div>

                <div style="background:#F8FAFC; border:1px solid #E2E8F0; padding:15px; border-radius:8px;">
                    <p style="color:#64748b; font-size:13px;">ROI</p>
                    <p style="font-size:20px; font-weight:bold;">
                        {{ $roi !== null ? $roi . '%' : '—' }}
                    </p>
                </div>

            </div>

            {{-- RINCIAN TRANSAKSI --}}
            <h4 style="font-size:18px; font-weight:bold; margin-bottom:12px;">
                Rincian Transaksi
            </h4>

            <div style="overflow-x:auto; margin-bottom:25px;">
                <table style="width:100%; border-collapse:collapse; min-width:600px;">
                    <thead>
                        <tr style="background:#F8FAFC;">
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Tanggal</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Jenis</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Deskripsi</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:right;">Jumlah</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($event->financialTransactions->sortBy('transaction_date') as $transaction)
                            <tr>
                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $transaction->transaction_date
                                        ? $transaction->transaction_date->format('d-m-Y')
                                        : '—' }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $transaction->transaction_type === 'Income'
                                        ? 'Pemasukan'
                                        : 'Pengeluaran' }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $transaction->description }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0; text-align:right;">
                                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:15px; border:1px solid #E2E8F0; text-align:center; color:#64748b;">
                                    Belum ada transaksi keuangan untuk acara ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- RUNDOWN --}}
            <h4 style="font-size:18px; font-weight:bold; margin-bottom:10px;">
                Rundown Acara
            </h4>

            <p style="margin-bottom:15px;">
                Total kegiatan:
                <strong>{{ $totalRundowns }}</strong>
                &nbsp;|&nbsp;
                Sudah dipublikasikan:
                <strong>{{ $publishedRundowns }}</strong>
            </p>

            <div style="overflow-x:auto; margin-bottom:25px;">
                <table style="width:100%; border-collapse:collapse; min-width:550px;">
                    <thead>
                        <tr style="background:#F8FAFC;">
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Kegiatan</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Mulai</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Selesai</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Publikasi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($event->rundownItems->sortBy('start_time') as $item)
                            <tr>
                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $item->activity_name }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $item->start_time
                                        ? $item->start_time->format('d-m-Y H:i')
                                        : '—' }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $item->end_time
                                        ? $item->end_time->format('d-m-Y H:i')
                                        : '—' }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $item->is_published ? 'Dipublikasikan' : 'Draft' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:15px; border:1px solid #E2E8F0; text-align:center; color:#64748b;">
                                    Belum ada kegiatan rundown.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VENDOR --}}
            <h4 style="font-size:18px; font-weight:bold; margin-bottom:10px;">
                Vendor
            </h4>

            <p style="margin-bottom:12px;">
                Total penugasan vendor:
                <strong>{{ $vendorCount }}</strong>
            </p>

            <div style="overflow-x:auto; margin-bottom:25px;">
                <table style="width:100%; border-collapse:collapse; min-width:550px;">
                    <thead>
                        <tr style="background:#F8FAFC;">
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Nama Vendor</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Layanan</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:right;">Biaya</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($event->vendorAssignments as $assignment)
                            <tr>
                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $assignment->vendor->vendor_name ?? '—' }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $assignment->service_description }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0; text-align:right;">
                                    Rp {{ number_format($assignment->agreed_cost, 0, ',', '.') }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $assignment->assignment_status }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:15px; border:1px solid #E2E8F0; text-align:center; color:#64748b;">
                                    Belum ada vendor yang ditugaskan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PERALATAN --}}
            <h4 style="font-size:18px; font-weight:bold; margin-bottom:10px;">
                Peralatan Acara
            </h4>

            <p style="margin-bottom:12px;">
                Total penugasan peralatan:
                <strong>{{ $equipmentCount }}</strong>
            </p>

            <div style="overflow-x:auto; margin-bottom:25px;">
                <table style="width:100%; border-collapse:collapse; min-width:500px;">
                    <thead>
                        <tr style="background:#F8FAFC;">
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Nama Peralatan</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Kategori</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:center;">Jumlah</th>
                            <th style="padding:10px; border:1px solid #E2E8F0; text-align:left;">Status Penggunaan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($event->equipmentAssignments as $assignment)
                            <tr>
                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $assignment->equipment->equipment_name ?? '—' }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $assignment->equipment->category->category_name ?? '—' }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0; text-align:center;">
                                    {{ $assignment->quantity }}
                                </td>

                                <td style="padding:10px; border:1px solid #E2E8F0;">
                                    {{ $assignment->usage_status }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:15px; border:1px solid #E2E8F0; text-align:center; color:#64748b;">
                                    Belum ada peralatan yang ditugaskan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- EVALUASI --}}
            <h4 style="font-size:18px; font-weight:bold; margin-bottom:12px;">
                Hasil Evaluasi
            </h4>

            @if ($event->evaluation)

                <div style="margin-bottom:15px;">
                    <strong>Catatan Efektivitas</strong>
                    <p style="margin-top:5px; white-space:pre-line;">
                        {{ $event->evaluation->effectiveness_notes ?: 'Belum diisi.' }}
                    </p>
                </div>

                <div style="margin-bottom:15px;">
                    <strong>Kelebihan Acara</strong>
                    <p style="margin-top:5px; white-space:pre-line;">
                        {{ $event->evaluation->strengths ?: 'Belum diisi.' }}
                    </p>
                </div>

                <div style="margin-bottom:15px;">
                    <strong>Hal yang Perlu Ditingkatkan</strong>
                    <p style="margin-top:5px; white-space:pre-line;">
                        {{ $event->evaluation->improvements ?: 'Belum diisi.' }}
                    </p>
                </div>

                <p style="font-size:13px; color:#64748b;">
                    Dievaluasi pada:
                    {{ $event->evaluation->evaluated_at
                        ? $event->evaluation->evaluated_at->format('d-m-Y H:i')
                        : '—' }}
                </p>

            @else

                <p style="color:#64748b;">
                    Evaluasi untuk acara ini belum dibuat.
                </p>

            @endif

        </div>

    @empty

        <div style="background:white; border:1px solid #E2E8F0; padding:30px; border-radius:10px; text-align:center;">
            <h3 style="font-size:20px; font-weight:bold;">
                Belum Ada Data Acara
            </h3>

            <p style="color:#64748b; margin-top:8px;">
                Laporan akan muncul setelah data acara tersedia.
            </p>
        </div>

    @endforelse

</div>

<style>
    @media print {
        body {
            background: white !important;
        }

        .ems-sidebar,
        button,
        a {
            display: none !important;
        }

        .ems-main {
            margin: 0 !important;
            padding: 0 !important;
        }

        .report-card {
            box-shadow: none !important;
            break-inside: avoid;
        }
    }
</style>
@endsection