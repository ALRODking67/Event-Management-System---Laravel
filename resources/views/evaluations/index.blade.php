@extends('layouts.ems')

@section('title', 'Evaluasi & ROI')
@section('page-heading', 'Evaluasi & ROI')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b;">
            Evaluasi & ROI
        </h2>

        <p style="color: #64748b; margin-top: 5px;">
            Evaluasi hasil pelaksanaan dan ringkasan keuangan setiap acara.
        </p>
    </div>

    @if (session('success'))
        <div style="padding: 12px; background: #dcfce7; color: #166534; border-radius: 6px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 6px; margin-bottom: 15px;">
            {{ session('error') }}
        </div>
    @endif

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
        @endphp

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 20px;">

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: #1e293b;">
                        {{ $event->event_name }}
                    </h3>

                    <p style="font-size: 13px; color: #64748b; margin-top: 4px;">
                        {{ $event->start_datetime?->format('d-m-Y H:i') ?? 'Jadwal belum ditentukan' }}
                        | Status: {{ $event->status }}
                    </p>
                </div>

                @if ($event->evaluation)
                    <a href="{{ route('evaluations.edit', [
                        'organization' => $organization->slug,
                        'evaluation' => $event->evaluation->id
                    ]) }}"
                       style="background: #2f65f6; color: white; padding: 9px 15px; border-radius: 6px; text-decoration: none;">
                        Edit Evaluasi
                    </a>
                @else
                    <a href="{{ route('evaluations.create', [
                        'organization' => $organization->slug,
                        'event' => $event->id
                    ]) }}"
                       style="background: #2f65f6; color: white; padding: 9px 15px; border-radius: 6px; text-decoration: none;">
                        Buat Evaluasi
                    </a>
                @endif
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-top: 20px;">

                <div style="background: #f8fafc; padding: 15px; border-radius: 7px;">
                    <p style="font-size: 13px; color: #64748b;">Total Pemasukan</p>
                    <p style="font-size: 18px; font-weight: 700; color: #15803d;">
                        Rp {{ number_format($income, 0, ',', '.') }}
                    </p>
                </div>

                <div style="background: #f8fafc; padding: 15px; border-radius: 7px;">
                    <p style="font-size: 13px; color: #64748b;">Total Pengeluaran</p>
                    <p style="font-size: 18px; font-weight: 700; color: #dc2626;">
                        Rp {{ number_format($expense, 0, ',', '.') }}
                    </p>
                </div>

                <div style="background: #f8fafc; padding: 15px; border-radius: 7px;">
                    <p style="font-size: 13px; color: #64748b;">Selisih Keuangan</p>
                    <p style="font-size: 18px; font-weight: 700; color: #1e293b;">
                        Rp {{ number_format($balance, 0, ',', '.') }}
                    </p>
                </div>

                <div style="background: #f8fafc; padding: 15px; border-radius: 7px;">
                    <p style="font-size: 13px; color: #64748b;">Estimasi ROI</p>
                    <p style="font-size: 18px; font-weight: 700; color: #1e293b;">
                        {{ $roi !== null ? $roi . '%' : '—' }}
                    </p>
                </div>

            </div>

            <div style="margin-top: 18px; border-top: 1px solid #e2e8f0; padding-top: 15px;">

                <p style="font-weight: 700; color: #334155; margin-bottom: 8px;">
                    Hasil Evaluasi
                </p>

                @if ($event->evaluation)
                    <p style="font-size: 14px; color: #475569;">
                        {{ $event->evaluation->effectiveness_notes ?: 'Belum ada catatan efektivitas.' }}
                    </p>
                @else
                    <p style="font-size: 14px; color: #94a3b8;">
                        Belum ada evaluasi untuk acara ini.
                    </p>
                @endif

            </div>

        </div>

    @empty
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 30px; text-align: center;">
            <p style="color: #64748b;">
                Belum ada acara dalam organisasi ini.
            </p>
        </div>
    @endforelse

</div>
@endsection
