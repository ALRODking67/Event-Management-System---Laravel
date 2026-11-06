<?php

namespace App\Http\Controllers;

use App\Models\EquipmentCategory;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentCategoryController extends Controller
{
    public function index(Organization $organization)
    {
        $categories = $organization->equipmentCategories()
            ->withCount('equipment')
            ->orderBy('category_name')
            ->get();

        return view(
            'equipment-categories.index',
            compact('categories', 'organization')
        );
    }

    public function create(Organization $organization)
    {
        return view('equipment-categories.create', compact('organization'));
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'category_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('equipment_categories', 'category_name')
                    ->where('organization_id', $organization->id),
            ],
        ]);

        $organization->equipmentCategories()->create($validated);

        return redirect()
            ->route('equipment-categories.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Kategori peralatan berhasil ditambahkan.');
    }

    public function edit(
        Organization $organization,
        EquipmentCategory $equipmentCategory
    ) {
        return view(
            'equipment-categories.edit',
            compact('equipmentCategory', 'organization')
        );
    }

    public function update(
        Request $request,
        Organization $organization,
        EquipmentCategory $equipmentCategory
    ) {
        $validated = $request->validate([
            'category_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('equipment_categories', 'category_name')
                    ->where('organization_id', $organization->id)
                    ->ignore($equipmentCategory->id),
            ],
        ]);

        $equipmentCategory->update($validated);

        return redirect()
            ->route('equipment-categories.index', [
                'organization' => $organization->slug,
            ])
            ->with('success', 'Kategori peralatan berhasil diperbarui.');
    }
}
