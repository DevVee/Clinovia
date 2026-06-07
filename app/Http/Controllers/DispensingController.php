<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dispensing\StoreDispensingRequest;
use App\Models\Consultation;
use App\Models\DispensingRecord;
use App\Models\Medicine;
use App\Models\Patient;
use App\Services\DispensingService;
use Illuminate\Http\Request;

class DispensingController extends Controller
{
    public function __construct(private readonly DispensingService $service) {}

    /* ------------------------------------------------------------------ */
    /*  INDEX                                                               */
    /* ------------------------------------------------------------------ */
    public function index(Request $request)
    {
        $this->authorize('view-dispensing');

        $search   = $request->get('search', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo   = $request->get('date_to', '');

        $records = DispensingRecord::with('patient', 'medicine', 'dispensedBy')
            ->when($search, fn ($q) => $q->whereHas('patient', fn ($q2) =>
                $q2->where('first_name', 'like', "%{$search}%")
                   ->orWhere('last_name',  'like', "%{$search}%")
            ))
            ->when($dateFrom, fn ($q) => $q->whereDate('dispensed_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->whereDate('dispensed_at', '<=', $dateTo))
            ->latest('dispensed_at')
            ->paginate(20)
            ->withQueryString();

        $filters = compact('search', 'dateFrom', 'dateTo');

        return view('dispensing.index', compact('records', 'filters'));
    }

    /* ------------------------------------------------------------------ */
    /*  CREATE / STORE                                                      */
    /* ------------------------------------------------------------------ */
    public function create(Request $request)
    {
        $this->authorize('create-dispensing');

        $patients = Patient::active()
            ->orderBy('last_name')->orderBy('first_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'patient_number']);

        $medicines = Medicine::with('category')
            ->active()
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'quantity', 'unit', 'category_id', 'low_stock_threshold']);

        $consultations = Consultation::whereDate('visit_date', '>=', now()->subDays(30))
            ->orderByDesc('visit_date')
            ->get(['id', 'patient_id', 'visit_date', 'chief_complaint']);

        $selectedPatient = $request->integer('patient_id') ?: null;

        return view('dispensing.create', compact('patients', 'medicines', 'consultations', 'selectedPatient'));
    }

    public function store(StoreDispensingRequest $request)
    {
        try {
            $record = $this->service->dispense($request->validated());
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
        }

        return redirect()
            ->route('dispensing.show', $record)
            ->with('success', 'Medicine dispensed successfully.');
    }

    /* ------------------------------------------------------------------ */
    /*  SHOW                                                                */
    /* ------------------------------------------------------------------ */
    public function show(DispensingRecord $dispensing)
    {
        $this->authorize('view-dispensing');

        $dispensing->load('patient', 'medicine.category', 'dispensedBy', 'consultation');

        return view('dispensing.show', compact('dispensing'));
    }
}
