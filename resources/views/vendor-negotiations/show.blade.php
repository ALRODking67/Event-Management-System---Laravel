@extends('layouts.ems')

@section('title', 'Detail Negosiasi Vendor')
@section('page-heading', 'Detail Negosiasi Vendor')

@section('content')
<div style="max-width:1100px;">

    <div style="margin-bottom:20px;">
        @if (in_array($roleName, ['Admin', 'Staf Operasional'], true))
            <a href="{{ route('vendor-confirmations.index', ['organization' => $organization->slug]) }}"
            style="color:#2563eb; text-decoration:none; font-weight:600;">
                &larr; Kembali ke Konfirmasi &amp; Negosiasi Vendor
            </a>
        @else
            <a href="{{ route('vendor-confirmations.index', ['organization' => $organization->slug]) }}"
            style="color:#2563eb; text-decoration:none; font-weight:600;">
                &larr; Kembali ke Negosiasi Vendor
            </a>
        @endif
    </div>

    @if (session('success'))
        <div style="background:#dcfce7; color:#166534; padding:14px 16px; border-radius:8px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background:#fee2e2; color:#991b1b; padding:14px 16px; border-radius:8px; margin-bottom:20px;">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul style="margin:8px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $latestOffer = $eventVendor->offers
            ->sortByDesc('created_at')
            ->first();

        $pendingOffer = $eventVendor->offers
            ->where('status', 'Pending')
            ->sortByDesc('created_at')
            ->first();

        $canRespond = $pendingOffer
            && (int) $pendingOffer->offered_by_user_id !== (int) auth()->id();

        $canCounter = !$eventVendor->agreement_accepted_at
            && (
                $canRespond
                || (
                    $latestOffer
                    && $latestOffer->status === 'Rejected'
                )
            );

        $counterBaseOffer = $pendingOffer ?? $latestOffer;
    @endphp

    {{-- INFORMASI PENUGASAN --}}
    <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; padding:22px; margin-bottom:22px;">
        <h2 style="font-size:18px; color:#1e293b; margin:0 0 18px;">
            Informasi Penugasan
        </h2>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
            <div>
                <p style="font-size:13px; color:#64748b; margin:0 0 5px;">Nama Acara</p>
                <strong style="color:#1e293b;">
                    {{ $eventVendor->event->event_name ?? 'Acara tidak ditemukan' }}
                </strong>
            </div>

            <div>
                <p style="font-size:13px; color:#64748b; margin:0 0 5px;">Nama Vendor</p>
                <strong style="color:#1e293b;">
                    {{ $eventVendor->vendor->vendor_name ?? 'Vendor tidak ditemukan' }}
                </strong>
            </div>

            <div>
                <p style="font-size:13px; color:#64748b; margin:0 0 5px;">Status Negosiasi</p>
                <strong style="color:#1e293b;">
                    {{ $eventVendor->agreement_status === 'agreed' ? 'Sudah Disepakati' : 'Dalam Negosiasi' }}
                </strong>
            </div>

            <div>
                <p style="font-size:13px; color:#64748b; margin:0 0 5px;">Status Penugasan</p>
                <strong style="color:#1e293b;">
                    {{ $eventVendor->assignment_status }}
                </strong>
            </div>
        </div>

        @if ($eventVendor->agreement_accepted_at)
            <div style="background:#dcfce7; color:#166534; padding:14px; border-radius:7px; margin-top:20px;">
                <strong>Kesepakatan telah disetujui.</strong>
                <p style="margin:5px 0 0; font-size:14px;">
                    Harga kesepakatan:
                    Rp {{ number_format((float) $eventVendor->agreed_cost, 0, ',', '.') }}
                </p>
                <p style="margin:5px 0 0; font-size:14px;">
                    Waktu persetujuan:
                    {{ $eventVendor->agreement_accepted_at->format('d-m-Y H:i') }}
                </p>
            </div>
        @endif
    </div>

    {{-- RIWAYAT PENAWARAN --}}
    <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; padding:22px; margin-bottom:22px;">
        <h2 style="font-size:18px; color:#1e293b; margin:0 0 18px;">
            Riwayat Penawaran
        </h2>

        @forelse ($eventVendor->offers->sortBy('created_at') as $offer)
            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:17px; margin-bottom:15px;">

                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
                    <div>
                        <strong style="color:#1e293b;">
                            {{ $offer->offeredBy->name ?? 'Pengguna' }}
                        </strong>

                        <span style="font-size:12px; color:#64748b; margin-left:5px;">
                            {{ $offer->offer_type === 'initial' ? 'Penawaran Awal' : ($offer->offer_type === 'amendment' ? 'Perubahan Kesepakatan' : 'Counteroffer') }}
                        </span>
                    </div>

                    @if ($offer->status === 'Accepted')
                        <span style="background:#dcfce7; color:#166534; padding:5px 9px; border-radius:5px; font-size:12px; font-weight:600;">
                            Diterima
                        </span>
                    @elseif ($offer->status === 'Rejected')
                        <span style="background:#fee2e2; color:#991b1b; padding:5px 9px; border-radius:5px; font-size:12px; font-weight:600;">
                            Ditolak
                        </span>
                    @elseif ($offer->status === 'Superseded')
                        <span style="background:#f1f5f9; color:#475569; padding:5px 9px; border-radius:5px; font-size:12px; font-weight:600;">
                            Digantikan Penawaran Baru
                        </span>
                    @else
                        <span style="background:#fef3c7; color:#92400e; padding:5px 9px; border-radius:5px; font-size:12px; font-weight:600;">
                            Menunggu Respons
                        </span>
                    @endif
                </div>

                <div style="font-size:13px; color:#64748b; margin-bottom:13px;">
                    Dikirim {{ $offer->created_at->format('d-m-Y H:i') }}
                </div>

                <div style="background:#f8fafc; padding:15px; border-radius:7px;">
                    <p style="margin:0 0 12px;">
                        <span style="color:#64748b; font-size:13px;">Harga Penawaran</span><br>
                        <strong style="font-size:20px; color:#1e293b;">
                            {{ $offer->amount !== null ? 'Rp ' . number_format((float) $offer->amount, 0, ',', '.') : 'Belum ditentukan' }}
                        </strong>
                    </p>

                    <p style="margin:0 0 10px;">
                        <strong style="font-size:13px; color:#475569;">Deskripsi Layanan</strong><br>
                        <span style="color:#334155; white-space:pre-line;">{{ $offer->scope_description ?: 'Tidak ada deskripsi.' }}</span>
                    </p>

                    @if ($offer->start_datetime || $offer->end_datetime)
                        <p style="margin:0 0 10px;">
                            <strong style="font-size:13px; color:#475569;">Jadwal Layanan</strong><br>
                            <span style="color:#334155;">
                                {{ $offer->start_datetime ? $offer->start_datetime->format('d-m-Y H:i') : '-' }}
                                sampai
                                {{ $offer->end_datetime ? $offer->end_datetime->format('d-m-Y H:i') : '-' }}
                            </span>
                        </p>
                    @endif

                    @if ($offer->payment_terms)
                        <p style="margin:0 0 10px;">
                            <strong style="font-size:13px; color:#475569;">Ketentuan Pembayaran</strong><br>
                            <span style="color:#334155; white-space:pre-line;">{{ $offer->payment_terms }}</span>
                        </p>
                    @endif

                    @if ($offer->notes)
                        <p style="margin:0;">
                            <strong style="font-size:13px; color:#475569;">Catatan</strong><br>
                            <span style="color:#334155; white-space:pre-line;">{{ $offer->notes }}</span>
                        </p>
                    @endif
                </div>

                @if ($offer->responded_at)
                    <p style="font-size:12px; color:#64748b; margin:12px 0 0;">
                        Direspons oleh {{ $offer->respondedBy->name ?? 'Pengguna' }}
                        pada {{ $offer->responded_at->format('d-m-Y H:i') }}.
                    </p>
                @endif

                @if ($offer->status === 'Rejected' && $offer->response_note)
                    <div style="background:#fef2f2; border:1px solid #fecaca; padding:12px; border-radius:7px; margin-top:12px;">
                        <strong style="font-size:13px; color:#991b1b;">
                            Alasan Penolakan
                        </strong>

                        <p style="font-size:13px; color:#7f1d1d; margin:6px 0 0; white-space:pre-line;">{{ $offer->response_note }}</p>
                    </div>
                @endif
            </div>
        @empty
            <p style="color:#64748b; margin:0;">
                Belum ada riwayat penawaran.
            </p>
        @endforelse
    </div>

    {{-- PERMINTAAN PERUBAHAN KEPUTUSAN --}}
    <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; padding:22px; margin-bottom:22px;">

        <h2 style="font-size:18px; color:#1e293b; margin:0 0 15px;">
            Riwayat Perubahan Keputusan
        </h2>

        @php
            $pendingDecisionRequest = $eventVendor->decisionRequests
                ->firstWhere('status', 'Pending');

            $canRequestDecision = $roleName === 'Vendor'
                && $latestOffer
                && $latestOffer->status === 'Rejected'
                && !$eventVendor->agreement_accepted_at
                && !$pendingDecisionRequest;
        @endphp

        @forelse ($eventVendor->decisionRequests as $decisionRequest)
            <div style="border:1px solid #e2e8f0; border-radius:8px; padding:16px; margin-bottom:13px;">

                <div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                    <strong style="color:#1e293b;">
                        {{ $decisionRequest->requester->name ?? 'Vendor' }}
                    </strong>

                    @if ($decisionRequest->status === 'Approved')
                        <span style="background:#dcfce7; color:#166534; padding:5px 9px; border-radius:5px; font-size:12px;">
                            Disetujui Admin
                        </span>
                    @elseif ($decisionRequest->status === 'Rejected')
                        <span style="background:#fee2e2; color:#991b1b; padding:5px 9px; border-radius:5px; font-size:12px;">
                            Ditolak Admin
                        </span>
                    @else
                        <span style="background:#fef3c7; color:#92400e; padding:5px 9px; border-radius:5px; font-size:12px;">
                            Menunggu Persetujuan
                        </span>
                    @endif
                </div>

                <p style="font-size:13px; color:#64748b; margin:8px 0;">
                    Meminta perubahan keputusan menjadi menerima penawaran pada
                    {{ $decisionRequest->created_at->format('d-m-Y H:i') }}.
                </p>

                @if ($decisionRequest->reviewed_at)
                    <p style="font-size:13px; color:#64748b; margin:5px 0;">
                        Ditinjau oleh {{ $decisionRequest->reviewer->name ?? 'Admin' }}
                        pada {{ $decisionRequest->reviewed_at->format('d-m-Y H:i') }}.
                    </p>
                @endif

                @if ($decisionRequest->admin_note)
                    <div style="background:#f8fafc; padding:12px; border-radius:6px; margin-top:10px;">
                        <strong style="font-size:13px; color:#475569;">Catatan Admin</strong>
                        <p style="font-size:14px; color:#334155; white-space:pre-line; margin:5px 0 0;">
                            {{ $decisionRequest->admin_note }}
                        </p>
                    </div>
                @endif

                @if ($roleName !== 'Vendor' && $decisionRequest->status === 'Pending')
                    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:15px;">

                        <form method="POST" action="{{ route('vendor-negotiations.decision-requests.review', [
                            'organization' => $organization->slug,
                            'eventVendor' => $eventVendor->id,
                            'decisionRequest' => $decisionRequest->id,
                        ]) }}">
                            @csrf
                            <input type="hidden" name="action" value="approve">

                            <button type="submit"
                                    onclick="return confirm('Setujui permintaan Vendor dan catat penawaran sebagai kesepakatan?')"
                                    style="background:#16a34a; color:white; border:0; padding:10px 15px; border-radius:6px; cursor:pointer; font-weight:600;">
                                Setujui Permintaan
                            </button>
                        </form>

                        <form method="POST" action="{{ route('vendor-negotiations.decision-requests.review', [
                            'organization' => $organization->slug,
                            'eventVendor' => $eventVendor->id,
                            'decisionRequest' => $decisionRequest->id,
                        ]) }}"
                        style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                            @csrf
                            <input type="hidden" name="action" value="reject">

                            <input type="text" name="admin_note" required maxlength="5000"
                                placeholder="Alasan penolakan wajib diisi"
                                style="min-width:230px; padding:10px; border:1px solid #cbd5e1; border-radius:6px;">

                            <button type="submit"
                                    onclick="return confirm('Tolak permintaan perubahan keputusan ini?')"
                                    style="background:#dc2626; color:white; border:0; padding:10px 15px; border-radius:6px; cursor:pointer; font-weight:600;">
                                Tolak Permintaan
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <p style="font-size:14px; color:#64748b;">
                Belum ada permintaan perubahan keputusan.
            </p>
        @endforelse

        @if ($canRequestDecision)
            <div style="background:#eff6ff; border-radius:8px; padding:16px; margin-top:15px;">
                <strong style="color:#1e40af;">Ingin menerima penawaran yang sebelumnya ditolak?</strong>

                <p style="font-size:14px; color:#64748b; margin-bottom:15px;">
                    Berubah pikiran dan ingin menerima kembali penawaran yang sebelumnya ditolak?
                    Kirim permintaan kepada Admin. Kesepakatan hanya berlaku jika Admin menyetujuinya.
                </p>

                <form method="POST" action="{{ route('vendor-negotiations.decision-requests.store', [
                    'organization' => $organization->slug,
                    'eventVendor' => $eventVendor->id,
                ]) }}">
                    @csrf

                    <button type="submit"
                        onclick="return confirm('Kirim permintaan untuk menerima kembali penawaran sebelumnya kepada Admin?')"
                        style="background:#2f65f6; color:white; border:0; padding:11px 17px; border-radius:7px; cursor:pointer; font-weight:600;">
                        Terima Penawaran Sebelumnya
                    </button>
                </form>
            </div>
        @elseif ($roleName === 'Vendor' && $pendingDecisionRequest)
            <p style="background:#eff6ff; color:#1e40af; padding:13px; border-radius:7px; margin-top:15px;">
                Permintaan perubahan keputusan sedang menunggu persetujuan Admin.
            </p>
        @endif
    </div>

    
    
    {{-- TINDAKAN RESPONS --}}
    @if ($canRespond)
        <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; padding:22px; margin-bottom:22px;">
            <h2 style="font-size:18px; color:#1e293b; margin:0 0 10px;">
                Respons Penawaran Terakhir
            </h2>

            <p style="color:#64748b; font-size:14px; margin:0 0 18px;">
                Silakan pilih salah satu tindakan. Setiap tindakan memiliki proses yang berbeda.
            </p>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">

                {{-- TERIMA PENAWARAN: LANGSUNG --}}
                <form method="POST" action="{{ route('vendor-negotiations.respond', [
                    'organization' => $organization->slug,
                    'eventVendor' => $eventVendor->id,
                    'offer' => $pendingOffer->id
                ]) }}">
                    @csrf
                    <input type="hidden" name="action" value="accept">

                    <button type="submit"
                        onclick="return confirm('Apakah kamu yakin menerima seluruh isi penawaran ini?')"
                        style="background:#16a34a; color:white; border:0; padding:11px 18px; border-radius:7px; cursor:pointer; font-weight:600;">
                        Terima Penawaran
                    </button>
                </form>

                {{-- TOLAK: BUKA FORM ALASAN --}}
                <button type="button"
                    onclick="document.getElementById('reject-form').style.display='block'; document.getElementById('counteroffer-form').style.display='none'; document.getElementById('reject-form').scrollIntoView({behavior:'smooth',block:'start'});"
                    style="background:#dc2626; color:white; border:0; padding:11px 18px; border-radius:7px; cursor:pointer; font-weight:600;">
                    Tolak Penawaran
                </button>

                {{-- TAWAR BALIK: BUKA FORM COUNTEROFFER --}}
                <button type="button"
                    onclick="document.getElementById('counteroffer-form').style.display='block'; document.getElementById('reject-form').style.display='none'; document.getElementById('counteroffer-form').scrollIntoView({behavior:'smooth',block:'start'});"
                    style="background:#2f65f6; color:white; border:0; padding:11px 18px; border-radius:7px; cursor:pointer; font-weight:600;">
                    Ajukan Penawaran Baru
                </button>
            </div>
        </div>
    @endif

    {{-- FORM ALASAN PENOLAKAN --}}
    @if ($canRespond)
        <div id="reject-form"
            style="display:none; background:white; border:1px solid #dc2626; border-radius:10px; padding:22px; margin-bottom:22px;">

            <h2 style="font-size:18px; color:#991b1b; margin:0 0 8px;">
                Alasan Penolakan Penawaran
            </h2>

            <p style="font-size:14px; color:#64748b; margin:0 0 18px;">
                Jelaskan alasan mengapa kamu tidak dapat menerima penawaran ini.
                Alasan akan disimpan dalam riwayat negosiasi.
            </p>

            <form method="POST" action="{{ route('vendor-negotiations.respond', [
                'organization' => $organization->slug,
                'eventVendor' => $eventVendor->id,
                'offer' => $pendingOffer->id
            ]) }}">
                @csrf
                <input type="hidden" name="action" value="reject">

                <div style="margin-bottom:18px;">
                    <label for="response_note"
                        style="display:block; font-weight:600; margin-bottom:7px;">
                        Alasan Penolakan <span style="color:#dc2626;">*</span>
                    </label>

                    <textarea id="response_note" name="response_note" rows="4" required
                        placeholder="Contoh: Harga penawaran belum sesuai dengan anggaran kami."
                        style="width:100%; padding:11px; border:1px solid #cbd5e1; border-radius:7px;">{{ old('response_note') }}</textarea>

                    @error('response_note')
                        <p style="color:#dc2626; font-size:13px; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    onclick="return confirm('Yakin ingin menolak penawaran ini?')"
                    style="background:#dc2626; color:white; border:0; padding:11px 20px; border-radius:7px; cursor:pointer; font-weight:600;">
                    Kirim Penolakan
                </button>

                <button type="button"
                    onclick="document.getElementById('reject-form').style.display='none';"
                    style="background:#e2e8f0; color:#334155; border:0; padding:11px 20px; border-radius:7px; cursor:pointer; margin-left:5px;">
                    Batal
                </button>
            </form>
        </div>
    @endif

    {{-- TOMBOL TAWAR BALIK SETELAH PENAWARAN DITOLAK --}}
    @if (!$canRespond && $roleName === 'Vendor' && $canCounter)
        <div style="background:white; border:1px solid #e2e8f0; border-radius:10px; padding:22px; margin-bottom:22px;">
            <p style="color:#64748b; font-size:14px; margin-bottom:15px;">
                Tidak cocok dengan harga atau ketentuan sebelumnya?
                Ajukan harga dan ketentuan baru kepada Admin untuk dinegosiasikan kembali.
            </p>

            <button type="button"
                onclick="document.getElementById('counteroffer-form').style.display='block'; document.getElementById('counteroffer-form').scrollIntoView({behavior:'smooth',block:'start'});"
                style="background:#2f65f6; color:white; border:0; padding:11px 18px; border-radius:7px; cursor:pointer; font-weight:600;">
                Ajukan Tawar Balik
            </button>
        </div>
    @endif

    {{-- STATUS MENUNGGU RESPONS --}}
    @if (!$canRespond && $pendingOffer)
        <div style="background:#eff6ff; color:#1e40af; padding:15px; border-radius:8px; margin-bottom:22px;">
            Penawaran terakhir sudah dikirim oleh kamu. Saat ini menunggu respons dari pihak lain.
        </div>
    @endif

    {{-- FORM COUNTEROFFER --}}
    @if ($canCounter)
        <div id="counteroffer-form"
            style="display:none; background:white; border:1px solid #2f65f6; border-radius:10px; padding:22px; margin-bottom:22px;">

            <h2 style="font-size:18px; color:#1e293b; margin:0 0 8px;">
                Form Tawar Balik
            </h2>

            <p style="font-size:14px; color:#64748b; margin:0 0 20px;">
                Masukkan harga dan ketentuan yang kamu ajukan. Penawaran sebelumnya tetap tersimpan dalam riwayat.
            </p>

            <form method="POST" action="{{ route('vendor-negotiations.counter', [
                'organization' => $organization->slug,
                'eventVendor' => $eventVendor->id
            ]) }}">
                @csrf

                <div style="margin-bottom:18px;">
                    <label for="amount" style="display:block; font-weight:600; margin-bottom:7px;">
                        Harga Penawaran (Rp) <span style="color:#dc2626;">*</span>
                    </label>

                    <input type="number" id="amount" name="amount" min="1" step="0.01" required
                        value="{{ old('amount', $counterBaseOffer->amount) }}"
                        style="width:100%; padding:11px; border:1px solid #cbd5e1; border-radius:7px;">

                    @error('amount')
                        <p style="color:#dc2626; font-size:13px; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:15px; margin-bottom:18px;">
                    <div>
                        <label for="start_datetime" style="display:block; font-weight:600; margin-bottom:7px;">
                            Mulai Layanan
                        </label>

                        <input type="datetime-local" id="start_datetime" name="start_datetime"
                            value="{{ old('start_datetime', $counterBaseOffer->start_datetime?->format('Y-m-d\TH:i')) }}"
                            style="width:100%; padding:11px; border:1px solid #cbd5e1; border-radius:7px;">
                    </div>

                    <div>
                        <label for="end_datetime" style="display:block; font-weight:600; margin-bottom:7px;">
                            Selesai Layanan
                        </label>

                        <input type="datetime-local" id="end_datetime" name="end_datetime"
                            value="{{ old('end_datetime', $counterBaseOffer->end_datetime?->format('Y-m-d\TH:i')) }}"
                            style="width:100%; padding:11px; border:1px solid #cbd5e1; border-radius:7px;">
                    </div>
                </div>

                <div style="margin-bottom:18px;">
                    <label for="scope_description" style="display:block; font-weight:600; margin-bottom:7px;">
                        Deskripsi Layanan <span style="color:#dc2626;">*</span>
                    </label>

                    <textarea id="scope_description" name="scope_description" rows="4" required
                        style="width:100%; padding:11px; border:1px solid #cbd5e1; border-radius:7px;">{{ old('scope_description', $counterBaseOffer->scope_description) }}</textarea>

                    @error('scope_description')
                        <p style="color:#dc2626; font-size:13px; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:18px;">
                    <label for="payment_terms" style="display:block; font-weight:600; margin-bottom:7px;">
                        Ketentuan Pembayaran
                    </label>

                    <textarea id="payment_terms" name="payment_terms" rows="3"
                        placeholder="Contoh: DP 30%, pelunasan setelah acara."
                        style="width:100%; padding:11px; border:1px solid #cbd5e1; border-radius:7px;">{{ old('payment_terms', $counterBaseOffer->payment_terms) }}</textarea>
                </div>

                <div style="margin-bottom:22px;">
                    <label for="notes" style="display:block; font-weight:600; margin-bottom:7px;">
                        Catatan Tambahan
                    </label>

                    <textarea id="notes" name="notes" rows="3"
                        style="width:100%; padding:11px; border:1px solid #cbd5e1; border-radius:7px;">{{ old('notes') }}</textarea>
                </div>

                <button type="submit"
                    style="background:#2f65f6; color:white; border:0; padding:11px 20px; border-radius:7px; cursor:pointer; font-weight:600;">
                    Kirim Tawar Balik
                </button>

                <button type="button"
                    onclick="document.getElementById('counteroffer-form').style.display='none';"
                    style="background:#e2e8f0; color:#334155; border:0; padding:11px 20px; border-radius:7px; cursor:pointer; margin-left:5px;">
                    Batal
                </button>
            </form>
        </div>
    @endif



</div>
@endsection