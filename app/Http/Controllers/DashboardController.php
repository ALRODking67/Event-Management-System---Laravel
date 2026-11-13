<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Event;
use App\Models\EventVendor;
use App\Models\Equipment;
use App\Models\FinancialTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        $memberships = auth()->user()
            ->organizationMemberships()
            ->with('organization')
            ->get();

        if ($memberships->count() === 1) {
            return redirect()->route('organizations.dashboard', [
                'organization' => $memberships->first()->organization->slug,
            ]);
        }

        return redirect()->route('organizations.index');
    }

    public function organization(Organization $organization)
    {
        $membership = request()->attributes->get('organizationMembership');

        if (!$membership || !$membership->role) {
            abort(403);
        }

        $roleName = $membership->role->role_name;
        $isVendor = $roleName === 'Vendor';
        $isAdmin = $roleName === 'Admin';
        $vendorId = $membership->vendor_id;

        $totalEvents = 0;
        $totalEquipment = null;
        $readyEquipment = null;
        $equipmentReadyPercentage = null;
        $vendorConfirmationResponses = 0;
        $pendingVendorConfirmations = 0;

        // Nilai keuangan hanya diisi untuk Admin dan Staf Operasional.
        $totalIncome = 0;
        $totalExpense = 0;
        $netBalance = 0;
        $estimatedRoi = null;

        if ($isVendor) {
            $assignments = EventVendor::with([
                'event.venue',
                'confirmations',
            ])
                ->whereNotNull('vendor_id')
                ->where('vendor_id', $vendorId)
                ->whereHas('event', function ($query) use ($organization) {
                    $query->where('organization_id', $organization->id);
                })
                ->get();

            $totalEvents = $assignments
                ->pluck('event_id')
                ->unique()
                ->count();

            $vendorConfirmationResponses = $assignments
                ->filter(function ($assignment) {
                    return $assignment->confirmations->contains(
                        fn ($confirmation) =>
                            $confirmation->confirmed_at !== null
                    );
                })
                ->count();

            $pendingVendorConfirmations = max(
                0,
                $assignments->count() - $vendorConfirmationResponses
            );

            $upcomingEvents = Event::with('venue')
                ->where('organization_id', $organization->id)
                ->where('start_datetime', '>=', now())
                ->whereHas('vendorAssignments', function ($query) use ($vendorId) {
                    $query->whereNotNull('vendor_id')
                        ->where('vendor_id', $vendorId);
                })
                ->orderBy('start_datetime')
                ->take(5)
                ->get();
        } else {
            $totalEvents = $organization->events()->count();

            $totalEquipment = Equipment::where(
                'organization_id',
                $organization->id
            )->count();

            $readyEquipment = Equipment::where(
                'organization_id',
                $organization->id
            )
                ->where('condition_status', 'Good')
                ->where('availability_status', 'Available')
                ->count();

            $equipmentReadyPercentage = $totalEquipment > 0
                ? round(($readyEquipment / $totalEquipment) * 100, 1)
                : null;

            $organizationAssignments = EventVendor::whereHas(
                'event',
                function ($query) use ($organization) {
                    $query->where('organization_id', $organization->id);
                }
            );

            $vendorConfirmationResponses = (clone $organizationAssignments)
                ->whereHas('confirmations', function ($query) {
                    $query->whereNotNull('confirmed_at')
                        ->whereIn('availability_status', [
                            'Available',
                            'Unavailable',
                        ]);
                })
                ->count();

            $pendingVendorConfirmations = max(
                0,
                (clone $organizationAssignments)->count()
                    - $vendorConfirmationResponses
            );

            // Ringkasan keuangan untuk Admin dan Staf Operasional.
            $transactions = FinancialTransaction::where(
                'organization_id',
                $organization->id
            );

            $totalIncome = (clone $transactions)
                ->where('transaction_type', 'Income')
                ->sum('amount');

            $totalExpense = (clone $transactions)
                ->where('transaction_type', 'Expense')
                ->sum('amount');

            $netBalance = $totalIncome - $totalExpense;

            $estimatedRoi = $totalExpense > 0
                ? round(($netBalance / $totalExpense) * 100, 1)
                : null;

            $upcomingEvents = Event::with('venue')
                ->where('organization_id', $organization->id)
                ->where('start_datetime', '>=', now())
                ->orderBy('start_datetime')
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'organization',
            'membership',
            'roleName',
            'isVendor',
            'isAdmin',
            'totalEvents',
            'totalEquipment',
            'readyEquipment',
            'equipmentReadyPercentage',
            'vendorConfirmationResponses',
            'pendingVendorConfirmations',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'estimatedRoi',
            'upcomingEvents'
        ));
    }
}