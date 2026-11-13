<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FinancialTransactionController extends Controller
{
    public function index(Organization $organization)
    {
        $transactions = FinancialTransaction::with(['event', 'recordedBy'])
            ->where('organization_id', $organization->id)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(10);

        $totalIncome = FinancialTransaction::where('organization_id', $organization->id)
            ->where('transaction_type', 'Income')
            ->sum('amount');

        $totalExpense = FinancialTransaction::where('organization_id', $organization->id)
            ->where('transaction_type', 'Expense')
            ->sum('amount');

        $netBalance = $totalIncome - $totalExpense;

        return view('financial-transactions.index', compact(
            'organization',
            'transactions',
            'totalIncome',
            'totalExpense',
            'netBalance'
        ));
    }

    public function create(Organization $organization)
    {
        $events = $organization->events()
            ->orderBy('event_name')
            ->get();

        return view('financial-transactions.create', compact(
            'organization',
            'events'
        ));
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $this->validateTransaction($request, $organization);

        FinancialTransaction::create([
            'organization_id' => $organization->id,
            'event_id' => $validated['event_id'],
            'transaction_type' => $validated['transaction_type'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'recorded_by' => auth()->id(),
        ]);

        return redirect()
            ->route('financial-transactions.index', $organization->slug)
            ->with('success', 'Transaksi keuangan berhasil ditambahkan.');
    }

    public function edit(Organization $organization, int $transaction)
    {
        $financialTransaction = $this->findTransaction($organization, $transaction);

        $events = $organization->events()
            ->orderBy('event_name')
            ->get();

        return view('financial-transactions.edit', compact(
            'organization',
            'financialTransaction',
            'events'
        ));
    }

    public function update(
        Request $request,
        Organization $organization,
        int $transaction
    ) {
        $financialTransaction = $this->findTransaction($organization, $transaction);
        $validated = $this->validateTransaction($request, $organization);

        $financialTransaction->update([
            'event_id' => $validated['event_id'],
            'transaction_type' => $validated['transaction_type'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
        ]);

        return redirect()
            ->route('financial-transactions.index', $organization->slug)
            ->with('success', 'Transaksi keuangan berhasil diperbarui.');
    }

    public function destroy(Organization $organization, int $transaction)
    {
        $financialTransaction = $this->findTransaction($organization, $transaction);
        $financialTransaction->delete();

        return redirect()
            ->route('financial-transactions.index', $organization->slug)
            ->with('success', 'Transaksi keuangan berhasil dihapus.');
    }

    private function findTransaction(Organization $organization, int $transaction)
    {
        return FinancialTransaction::where('organization_id', $organization->id)
            ->whereKey($transaction)
            ->firstOrFail();
    }

    private function validateTransaction(Request $request, Organization $organization): array
    {
        return $request->validate([
            'event_id' => [
                'required',
                'integer',
                Rule::exists('events', 'id')->where(
                    fn ($query) => $query->where('organization_id', $organization->id)
                ),
            ],
            'transaction_type' => ['required', Rule::in(['Income', 'Expense'])],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999999.99'],
            'transaction_date' => ['required', 'date'],
        ], [
            'event_id.required' => 'Pilih acara terlebih dahulu.',
            'event_id.exists' => 'Acara tidak valid untuk organisasi ini.',
            'transaction_type.required' => 'Pilih jenis transaksi.',
            'description.required' => 'Keterangan transaksi wajib diisi.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.min' => 'Nominal transaksi harus lebih dari nol.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
        ]);
    }
}