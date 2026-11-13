@extends('layouts.ems')

@section('title', 'Manajemen Keuangan')
@section('page-heading', 'Manajemen Keuangan')

@section('content')
<style>
    .finance-page { display: flex; flex-direction: column; gap: 22px; }
    .finance-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }
    .finance-card, .finance-table-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
    }
    .finance-card-label { color: #64748b; font-size: 13px; margin-bottom: 10px; }
    .finance-card-value { font-size: 23px; font-weight: 700; color: #0f172a; }
    .finance-card.income .finance-card-value { color: #059669; }
    .finance-card.expense .finance-card-value { color: #dc2626; }
    .finance-card.balance .finance-card-value { color: {{ $netBalance >= 0 ? '#059669' : '#dc2626' }}; }
    .finance-toolbar {
        display: flex; justify-content: space-between;
        align-items: center; gap: 12px; margin-bottom: 18px;
    }
    .finance-toolbar h3 { margin: 0; font-size: 17px; font-weight: 700; }
    .finance-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 10px 15px; border-radius: 9px; text-decoration: none;
        border: 0; cursor: pointer; font-size: 13px; font-weight: 600;
    }
    .finance-btn-primary { background: #2563eb; color: white; }
    .finance-btn-edit { background: #eff6ff; color: #2563eb; }
    .finance-btn-delete { background: #fef2f2; color: #dc2626; }
    .finance-table-wrap { overflow-x: auto; }
    .finance-table { width: 100%; border-collapse: collapse; min-width: 750px; }
    .finance-table th {
        text-align: left; padding: 12px 10px; color: #64748b;
        font-size: 12px; border-bottom: 1px solid #e2e8f0;
    }
    .finance-table td {
        padding: 14px 10px; border-bottom: 1px solid #f1f5f9;
        font-size: 13px; color: #334155;
    }
    .finance-type {
        display: inline-block; padding: 5px 9px; border-radius: 20px;
        font-size: 11px; font-weight: 700;
    }
    .finance-type.income { color: #047857; background: #d1fae5; }
    .finance-type.expense { color: #b91c1c; background: #fee2e2; }
    .finance-actions { display: flex; gap: 7px; }
    .finance-empty { text-align: center; padding: 35px 15px; color: #64748b; }
    .finance-alert {
        padding: 12px 15px; border-radius: 9px;
        background: #ecfdf5; color: #047857; font-size: 13px;
    }
    .finance-pagination { margin-top: 18px; }
    @media(max-width: 850px) {
        .finance-summary { grid-template-columns: 1fr; }
        .finance-toolbar { align-items: flex-start; flex-direction: column; }
    }
</style>

<div class="finance-page">

    @if (session('success'))
        <div class="finance-alert">{{ session('success') }}</div>
    @endif

    <div class="finance-summary">
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
            <div class="finance-card-label">Selisih Pemasukan dan Pengeluaran</div>
            <div class="finance-card-value">
                Rp{{ number_format((float) $netBalance, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="finance-table-card">
        <div class="finance-toolbar">
            <div>
                <h3>Riwayat Transaksi</h3>
                <div style="font-size:12px;color:#64748b;margin-top:5px;">
                    Catatan pemasukan dan pengeluaran organisasi
                </div>
            </div>

            <a href="{{ route('financial-transactions.create', ['organization' => $organization->slug]) }}"
               class="finance-btn finance-btn-primary">
                + Tambah Transaksi
            </a>
        </div>

        <div class="finance-table-wrap">
            <table class="finance-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Acara</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                        <th>Dicatat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_date?->format('d/m/Y') }}</td>
                            <td>{{ $transaction->event?->event_name ?? '-' }}</td>
                            <td>
                                @if ($transaction->transaction_type === 'Income')
                                    <span class="finance-type income">Pemasukan</span>
                                @else
                                    <span class="finance-type expense">Pengeluaran</span>
                                @endif
                            </td>
                            <td>{{ $transaction->description }}</td>
                            <td style="font-weight:700;white-space:nowrap;">
                                Rp{{ number_format((float) $transaction->amount, 0, ',', '.') }}
                            </td>
                            <td>{{ $transaction->recordedBy?->name ?? '-' }}</td>
                            <td>
                                <div class="finance-actions">
                                    <a class="finance-btn finance-btn-edit"
                                       href="{{ route('financial-transactions.edit', [
                                           'organization' => $organization->slug,
                                           'transaction' => $transaction->id
                                       ]) }}">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('financial-transactions.destroy', [
                                              'organization' => $organization->slug,
                                              'transaction' => $transaction->id
                                          ]) }}"
                                          onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="finance-btn finance-btn-delete">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="finance-empty">
                                Belum ada transaksi keuangan. Klik tombol
                                <strong>Tambah Transaksi</strong> untuk mulai mencatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="finance-pagination">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection