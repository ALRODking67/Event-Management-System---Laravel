@php
    $isEdit = $financialTransaction !== null;
@endphp

<style>
    .finance-form-card {
        background: #fff; border: 1px solid #e2e8f0;
        border-radius: 14px; padding: 24px; max-width: 760px;
    }
    .finance-form-title { font-size: 17px; font-weight: 700; margin-bottom: 22px; }
    .finance-field { margin-bottom: 18px; }
    .finance-field label {
        display: block; font-size: 13px; font-weight: 600;
        color: #334155; margin-bottom: 8px;
    }
    .finance-field input, .finance-field select, .finance-field textarea {
        width: 100%; padding: 11px 12px; border: 1px solid #cbd5e1;
        border-radius: 9px; font-size: 13px; background: white;
        color: #0f172a; box-sizing: border-box;
    }
    .finance-field input:focus, .finance-field select:focus,
    .finance-field textarea:focus {
        outline: none; border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .finance-error { color: #dc2626; font-size: 12px; margin-top: 5px; }
    .finance-form-actions { display: flex; gap: 10px; margin-top: 24px; }
    .finance-submit, .finance-cancel {
        border: 0; border-radius: 9px; padding: 11px 17px;
        font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none;
    }
    .finance-submit { background: #2563eb; color: white; }
    .finance-cancel { background: #f1f5f9; color: #475569; }
</style>

<div class="finance-form-card">
    <div class="finance-form-title">{{ $formTitle }}</div>

    <form method="POST" action="{{ $formAction }}">
        @csrf
        @if ($method === 'PUT')
            @method('PUT')
        @endif

        <div class="finance-field">
            <label for="event_id">Acara</label>
            <select name="event_id" id="event_id" required>
                <option value="">-- Pilih Acara --</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}"
                        @selected(old('event_id', $financialTransaction?->event_id) == $event->id)>
                        {{ $event->event_name }}
                    </option>
                @endforeach
            </select>
            @error('event_id')
                <div class="finance-error">{{ $message }}</div>
            @enderror
            @if ($events->isEmpty())
                <div class="finance-error">
                    Organisasi belum memiliki acara. Buat acara terlebih dahulu sebelum mencatat transaksi.
                </div>
            @endif
        </div>

        <div class="finance-field">
            <label for="transaction_type">Jenis Transaksi</label>
            <select name="transaction_type" id="transaction_type" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="Income"
                    @selected(old('transaction_type', $financialTransaction?->transaction_type) === 'Income')>
                    Pemasukan
                </option>
                <option value="Expense"
                    @selected(old('transaction_type', $financialTransaction?->transaction_type) === 'Expense')>
                    Pengeluaran
                </option>
            </select>
            @error('transaction_type')
                <div class="finance-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="finance-field">
            <label for="description">Keterangan Transaksi</label>
            <input type="text" name="description" id="description"
                   maxlength="255" required
                   placeholder="Contoh: Pembayaran tiket peserta"
                   value="{{ old('description', $financialTransaction?->description) }}">
            @error('description')
                <div class="finance-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="finance-field">
            <label for="amount">Nominal (Rupiah)</label>
            <input type="number" name="amount" id="amount"
                   min="1" step="1" required
                   placeholder="Contoh: 1500000"
                   value="{{ old('amount', $financialTransaction?->amount) }}">
            @error('amount')
                <div class="finance-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="finance-field">
            <label for="transaction_date">Tanggal Transaksi</label>
            <input type="date" name="transaction_date" id="transaction_date"
                   required
                   value="{{ old(
                       'transaction_date',
                       $financialTransaction?->transaction_date?->format('Y-m-d') ?? now()->format('Y-m-d')
                   ) }}">
            @error('transaction_date')
                <div class="finance-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="finance-form-actions">
            <button type="submit" class="finance-submit"
                @disabled($events->isEmpty())>
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
            </button>

            <a href="{{ route('financial-transactions.index', ['organization' => $organization->slug]) }}"
               class="finance-cancel">
                Batal
            </a>
        </div>
    </form>
</div>