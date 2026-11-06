<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Equipment;
use App\Models\EventVendor;
use App\Models\FinancialTransaction;
use App\Models\OrganizationMember;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Ambil organisasi yang diikuti pengguna.
        $organizationIds = OrganizationMember::where('user_id', $user->id)
            ->pluck('organization_id');

        // 1. Total acara dalam organisasi pengguna.
        $totalEvents = Event::whereIn('organization_id', $organizationIds)
            ->count();

        // 2. Kesiapan peralatan.
        $totalEquipment = Equipment::whereIn(
            'organization_id',
            $organizationIds
        )->count();

        $readyEquipment = Equipment::whereIn(
            'organization_id',
            $organizationIds
        )
            ->where('condition_status', 'Good')
            ->where('availability_status', 'Available')
            ->count();

        $equipmentReadyPercentage = $totalEquipment > 0
            ? round(($readyEquipment / $totalEquipment) * 100, 1)
            : null;

        // 3. Respons konfirmasi vendor.
        $vendorConfirmationResponses = EventVendor::whereHas(
            'event',
            function ($query) use ($organizationIds) {
                $query->whereIn('organization_id', $organizationIds);
            }
        )
            ->whereHas('confirmations', function ($query) {
                $query->whereNotNull('confirmed_at')
                    ->whereIn('availability_status', [
                        'Available',
                        'Unavailable',
                    ]);
            })
            ->count();

        // 4. Estimasi ROI.
        $totalIncome = FinancialTransaction::whereIn(
            'organization_id',
            $organizationIds
        )
            ->where('transaction_type', 'Income')
            ->sum('amount');

        $totalExpense = FinancialTransaction::whereIn(
            'organization_id',
            $organizationIds
        )
            ->where('transaction_type', 'Expense')
            ->sum('amount');

        $estimatedRoi = $totalExpense > 0
            ? round(
                (($totalIncome - $totalExpense) / $totalExpense) * 100,
                1
            )
            : null;

        // 5. Jadwal acara mendatang.
        $upcomingEvents = Event::with('venue')
            ->whereIn('organization_id', $organizationIds)
            ->where('start_datetime', '>=', now())
            ->orderBy('start_datetime')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalEvents',
            'totalEquipment',
            'readyEquipment',
            'equipmentReadyPercentage',
            'vendorConfirmationResponses',
            'estimatedRoi',
            'upcomingEvents'
        ));
    }
}
