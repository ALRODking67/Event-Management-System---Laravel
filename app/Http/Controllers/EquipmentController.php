<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    public function index(Organization $organization)
    {
        $equipment = $organization->equipment()
            ->with('category')
            ->orderBy('equipment_name')
            ->get();

        return view('equipment.index', compact('equipment', 'organization'));
    }

    public function create(Organization $organization)
    {
        $categories = $organization->equipmentCategories()
            ->orderBy('category_name')
            ->get();

        return view('equipment.create', compact('categories', 'organization'));
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('equipment_categories', 'id')
                    ->where('organization_id', $organization->id),
            ],
            'equipment_name' => ['required', 'string', 'max:150'],
            'asset_code' => [
                'required',
                'string',
                'max:80',
                Rule::unique('equipment', 'asset_code')
                    ->where('organization_id', $organization->id),
            ],
            'condition_status' => [
                'required',
                Rule::in(['Good', 'Minor Damage', 'Major Damage', 'Broken']),
            ],
            'availability_status' => [
                'required',
                Rule::in(['Available', 'In Use', 'Maintenance', 'Unavailable']),
            ],
        ]);

        $organization->equipment()->create($validated);

        return redirect()
            ->route('equipment.index', ['organization' => $organization->slug])
            ->with('success', 'Peralatan berhasil ditambahkan.');
    }

    public function edit(Organization $organization, Equipment $equipment)
    {
        $categories = $organization->equipmentCategories()
            ->orderBy('category_name')
            ->get();

        return view('equipment.edit', compact('equipment', 'categories', 'organization'));
    }

    public function update(
        Request $request,
        Organization $organization,
        Equipment $equipment
    ) {
        $validated = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('equipment_categories', 'id')
                    ->where('organization_id', $organization->id),
            ],
            'equipment_name' => ['required', 'string', 'max:150'],
            'asset_code' => [
                'required',
                'string',
                'max:80',
                Rule::unique('equipment', 'asset_code')
                    ->where('organization_id', $organization->id)
                    ->ignore($equipment->id),
            ],
            'condition_status' => [
                'required',
                Rule::in(['Good', 'Minor Damage', 'Major Damage', 'Broken']),
            ],
            'availability_status' => [
                'required',
                Rule::in(['Available', 'In Use', 'Maintenance', 'Unavailable']),
            ],
        ]);

        $equipment->update($validated);

        return redirect()
            ->route('equipment.index', ['organization' => $organization->slug])
            ->with('success', 'Data peralatan berhasil diperbarui.');
    }
}
