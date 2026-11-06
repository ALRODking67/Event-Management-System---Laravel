<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::with('category')
            ->orderBy('equipment_name')
            ->get();

        return view('equipment.index', compact('equipment'));
    }

    public function create()
    {
        $categories = EquipmentCategory::orderBy('category_name')->get();

        return view('equipment.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:equipment_categories,id'],
            'equipment_name' => ['required', 'string', 'max:150'],
            'asset_code' => ['required', 'string', 'max:80', 'unique:equipment,asset_code'],
            'condition_status' => [
                'required',
                Rule::in(['Good', 'Minor Damage', 'Major Damage', 'Broken']),
            ],
            'availability_status' => [
                'required',
                Rule::in(['Available', 'In Use', 'Maintenance', 'Unavailable']),
            ],
        ]);

        Equipment::create($validated);

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Peralatan berhasil ditambahkan.');
    }

    public function edit(Equipment $equipment)
    {
        $categories = EquipmentCategory::orderBy('category_name')->get();

        return view('equipment.edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:equipment_categories,id'],
            'equipment_name' => ['required', 'string', 'max:150'],
            'asset_code' => [
                'required',
                'string',
                'max:80',
                Rule::unique('equipment', 'asset_code')->ignore($equipment->id),
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
            ->route('equipment.index')
            ->with('success', 'Data peralatan berhasil diperbarui.');
    }
}