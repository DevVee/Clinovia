<?php

namespace App\Http\Controllers;

use App\Models\MedicineCategory;
use Illuminate\Http\Request;

class MedicineCategoryController extends Controller
{
    public function index()
    {
        $this->authorize('view-medicines');

        $categories = MedicineCategory::withCount('medicines')
            ->orderBy('name')
            ->get();

        return view('medicine-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create-medicines');

        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:medicine_categories,name',
            'description' => 'nullable|string|max:255',
        ]);

        MedicineCategory::create($data);

        return redirect()
            ->route('medicine-categories.index')
            ->with('success', 'Category "' . $data['name'] . '" created.');
    }

    public function edit(MedicineCategory $medicineCategory)
    {
        $this->authorize('update-medicines');

        return view('medicine-categories.edit', compact('medicineCategory'));
    }

    public function update(Request $request, MedicineCategory $medicineCategory)
    {
        $this->authorize('update-medicines');

        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:medicine_categories,name,' . $medicineCategory->id,
            'description' => 'nullable|string|max:255',
        ]);

        $medicineCategory->update($data);

        return redirect()
            ->route('medicine-categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(MedicineCategory $medicineCategory)
    {
        $this->authorize('delete-medicines');

        if ($medicineCategory->medicines()->count() > 0) {
            return back()->with('error', 'Cannot delete "' . $medicineCategory->name . '" — it has medicines assigned to it. Reassign them first.');
        }

        $name = $medicineCategory->name;
        $medicineCategory->delete();

        return redirect()
            ->route('medicine-categories.index')
            ->with('success', 'Category "' . $name . '" deleted.');
    }
}
