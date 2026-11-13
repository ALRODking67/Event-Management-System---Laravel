<?php

namespace App\Http\Controllers;

use App\Models\Organization;

class ReportController extends Controller
{
    public function index(Organization $organization)
    {
        $events = $organization->events()
            ->with([
                'venue',
                'evaluation',
                'financialTransactions',
                'rundownItems',
                'vendorAssignments.vendor',
                'equipmentAssignments.equipment.category',
            ])
            ->orderByDesc('start_datetime')
            ->get();

        return view('reports.index', compact(
            'organization',
            'events'
        ));
    }
}
