<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::withCount('eventAssignments')
            ->orderBy('vendor_name')
            ->get();

        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => [
                'required',
                'string',
                'max:150',
            ],
            'contact_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'email' => [
                'nullable',
                'email',
                'max:150',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'service_type' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        Vendor::create($validated);

        return redirect()
            ->route('vendors.index')
            ->with('success', 'Data vendor berhasil ditambahkan.');
    }

    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'vendor_name' => [
                'required',
                'string',
                'max:150',
            ],
            'contact_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'email' => [
                'nullable',
                'email',
                'max:150',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'service_type' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $vendor->update($validated);

        return redirect()
            ->route('vendors.index')
            ->with('success', 'Data vendor berhasil diperbarui.');
    }
}