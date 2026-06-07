<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\StockInRequest;
use App\Http\Requests\Inventory\StockOutRequest;
use App\Models\InventoryTransaction;
use App\Models\Medicine;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(private readonly InventoryService $service) {}

    /* ------------------------------------------------------------------ */
    /*  INDEX — medicines with current stock                               */
    /* ------------------------------------------------------------------ */
    public function index(Request $request)
    {
        $this->authorize('view-inventory');

        $search   = $request->get('search', '');
        $category = $request->get('category', '');

        $medicines = Medicine::with('category')
            ->when($search,   fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($category, fn ($q) => $q->where('category_id', $category))
            ->active()
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        $categories = \App\Models\MedicineCategory::orderBy('name')->get();

        return view('inventory.index', compact('medicines', 'categories', 'search', 'category'));
    }

    /* ------------------------------------------------------------------ */
    /*  STOCK IN                                                            */
    /* ------------------------------------------------------------------ */
    public function stockInForm(Request $request)
    {
        $this->authorize('manage-inventory');

        $medicines = Medicine::active()->orderBy('name')->get();
        $selected  = $request->integer('medicine_id') ?: null;

        return view('inventory.stock-in', compact('medicines', 'selected'));
    }

    public function stockIn(StockInRequest $request)
    {
        $medicine = Medicine::findOrFail($request->medicine_id);
        $this->service->stockIn($medicine, $request->validated());

        return redirect()
            ->route('inventory.index')
            ->with('success', "Stock added: +{$request->quantity} {$medicine->unit}(s) of \"{$medicine->name}\".");
    }

    /* ------------------------------------------------------------------ */
    /*  STOCK OUT                                                           */
    /* ------------------------------------------------------------------ */
    public function stockOutForm(Request $request)
    {
        $this->authorize('manage-inventory');

        $medicines = Medicine::active()->orderBy('name')->get();
        $selected  = $request->integer('medicine_id') ?: null;

        return view('inventory.stock-out', compact('medicines', 'selected'));
    }

    public function stockOut(StockOutRequest $request)
    {
        $medicine = Medicine::findOrFail($request->medicine_id);

        try {
            $this->service->stockOut($medicine, $request->validated());
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
        }

        return redirect()
            ->route('inventory.index')
            ->with('success', "Stock removed: -{$request->quantity} {$medicine->unit}(s) of \"{$medicine->name}\".");
    }

    /* ------------------------------------------------------------------ */
    /*  TRANSACTIONS LEDGER                                                 */
    /* ------------------------------------------------------------------ */
    public function transactions(Request $request)
    {
        $this->authorize('view-inventory');

        $search   = $request->get('search', '');
        $type     = $request->get('type', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo   = $request->get('date_to', '');

        $transactions = InventoryTransaction::with(['medicine', 'performedBy'])
            ->when($search,   fn ($q) => $q->whereHas('medicine', fn ($q2) => $q2->where('name', 'like', "%{$search}%")))
            ->when($type,     fn ($q) => $q->where('transaction_type', $type))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $filters = compact('search', 'type', 'dateFrom', 'dateTo');

        return view('inventory.transactions', compact('transactions', 'filters'));
    }
}
