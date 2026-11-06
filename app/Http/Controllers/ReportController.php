<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $events = Event::with([
            'venue',
            'evaluation',
            'financialTransactions',
            'rundownItems',
            'vendorAssignments.vendor',
            'equipmentAssignments.equipment',
        ])
            ->orderByDesc('start_datetime')
            ->get();

        return view('reports.index', compact('events'));
    }
}