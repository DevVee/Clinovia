<?php

namespace App\Http\Controllers;

use App\Http\Requests\Medicine\StoreMedicineRequest;
use App\Http\Requests\Medicine\UpdateMedicineRequest;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    /* ------------------------------------------------------------------ */
    /*  INDEX                                                               */
    /* ------------------------------------------------------------------ */
    public function index(Request $request)
    {
        $this->authorize('view-medicines');

        $search      = $request->get('search', '');
        $category    = $request->get('category', '');
        $stockFilter = $request->get('stock', '');        // low | out | all

        $medicines = Medicine::with('category')
            ->when($search,   fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($category, fn ($q) => $q->where('category_id', $category))
            ->when($stockFilter === 'low', fn ($q) => $q->lowStock())
            ->when($stockFilter === 'out', fn ($q) => $q->where('quantity', 0))
            ->active()
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $categories = MedicineCategory::orderBy('name')->get();

        return view('medicines.index', [
            'medicines'   => $medicines,
            'categories'  => $categories,
            'filters'     => compact('search', 'category', 'stockFilter'),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  CREATE / STORE                                                      */
    /* ------------------------------------------------------------------ */
    public function create()
    {
        $this->authorize('create-medicines');

        $categories = MedicineCategory::orderBy('name')->get();

        return view('medicines.create', compact('categories'));
    }

    public function store(StoreMedicineRequest $request)
    {
        $data               = $request->validated();
        $data['created_by'] = auth()->id();
        $data['is_active']  = $request->boolean('is_active', true);

        $medicine = Medicine::create($data);

        return redirect()
            ->route('medicines.show', $medicine)
            ->with('success', 'Medicine "' . $medicine->name . '" added successfully.');
    }

    /* ------------------------------------------------------------------ */
    /*  SHOW                                                                */
    /* ------------------------------------------------------------------ */
    public function show(Medicine $medicine)
    {
        $this->authorize('view-medicines');

        $medicine->load('category', 'createdBy');

        $transactions = $medicine->inventoryTransactions()
            ->with('performedBy')
            ->latest()
            ->limit(10)
            ->get();

        return view('medicines.show', compact('medicine', 'transactions'));
    }

    /* ------------------------------------------------------------------ */
    /*  EDIT / UPDATE                                                       */
    /* ------------------------------------------------------------------ */
    public function edit(Medicine $medicine)
    {
        $this->authorize('update-medicines');

        $categories = MedicineCategory::orderBy('name')->get();

        return view('medicines.edit', compact('medicine', 'categories'));
    }

    public function update(UpdateMedicineRequest $request, Medicine $medicine)
    {
        $this->authorize('update-medicines');

        $data              = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $medicine->update($data);

        return redirect()
            ->route('medicines.show', $medicine)
            ->with('success', 'Medicine "' . $medicine->name . '" updated successfully.');
    }

    /* ------------------------------------------------------------------ */
    /*  DESTROY                                                             */
    /* ------------------------------------------------------------------ */
    public function destroy(Medicine $medicine)
    {
        $this->authorize('delete-medicines');

        $medicine->delete();

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Medicine removed from inventory.');
    }

    /* ------------------------------------------------------------------ */
    /*  LOW STOCK                                                           */
    /* ------------------------------------------------------------------ */
    public function lowStock()
    {
        $this->authorize('view-medicines');

        $medicines = Medicine::with('category')
            ->active()->lowStock()
            ->orderBy('quantity')
            ->paginate(20);

        return view('medicines.low-stock', compact('medicines'));
    }

    /* ------------------------------------------------------------------ */
    /*  EXPIRING                                                            */
    /* ------------------------------------------------------------------ */
    public function expiring(Request $request)
    {
        $this->authorize('view-medicines');

        $days = $request->integer('days', 30);
        $days = in_array($days, [7, 14, 30, 60, 90]) ? $days : 30;

        $medicines = Medicine::with('category')
            ->active()->expiringSoon($days)
            ->orderBy('expiration_date')
            ->paginate(20);

        return view('medicines.expiring', compact('medicines', 'days'));
    }
}
