<?php

namespace App\Http\Controllers;

use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\Models\Patient;
use App\Repositories\Contracts\PatientRepositoryInterface;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function __construct(
        private readonly PatientRepositoryInterface $patients,
        private readonly PatientService             $service,
    ) {}

    /* ------------------------------------------------------------------ */
    /*  INDEX                                                               */
    /* ------------------------------------------------------------------ */
    public function index(Request $request)
    {
        $this->authorize('view-patients');

        $filters  = $request->only(['search', 'category', 'sex', 'is_active', 'sort', 'dir']);
        $patients = $this->patients->paginate($filters, 20);

        return view('patients.index', [
            'patients'       => $patients,
            'filters'        => $filters,
            'categoryLabels' => Patient::categoryLabels(),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  CREATE / STORE                                                      */
    /* ------------------------------------------------------------------ */
    public function create()
    {
        $this->authorize('create-patients');

        return view('patients.create', [
            'categoryLabels' => Patient::categoryLabels(),
            'bloodTypes'     => Patient::bloodTypes(),
        ]);
    }

    public function store(StorePatientRequest $request)
    {
        // CRITICAL-6 FIX: Wrap the entire create+generatePatientNumber in a single
        // DB transaction so the number generation lock and the INSERT are atomic.
        $patient = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['patient_number'] = $this->service->generatePatientNumber();
            $data['created_by']     = auth()->id();

            return $this->patients->create($data);
        });

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', "Patient {$patient->full_name} ({$patient->patient_number}) created successfully.");
    }

    /* ------------------------------------------------------------------ */
    /*  SHOW                                                                */
    /* ------------------------------------------------------------------ */
    public function show(Patient $patient)
    {
        $this->authorize('view-patients');

        $history = $this->service->getHealthHistory($patient);

        return view('patients.show', [
            'patient' => $patient,
            'history' => $history,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  EDIT / UPDATE                                                       */
    /* ------------------------------------------------------------------ */
    public function edit(Patient $patient)
    {
        $this->authorize('update-patients');

        return view('patients.edit', [
            'patient'        => $patient,
            'categoryLabels' => Patient::categoryLabels(),
            'bloodTypes'     => Patient::bloodTypes(),
        ]);
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $data               = $request->validated();
        $data['updated_by'] = auth()->id();

        $this->patients->update($patient, $data);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', "Patient {$patient->full_name} updated successfully.");
    }

    /* ------------------------------------------------------------------ */
    /*  DESTROY                                                             */
    /* ------------------------------------------------------------------ */
    public function destroy(Patient $patient)
    {
        $this->authorize('delete-patients');

        $name = $patient->full_name;
        $this->patients->delete($patient);

        return redirect()
            ->route('patients.index')
            ->with('success', "Patient {$name} has been deleted.");
    }

    /* ------------------------------------------------------------------ */
    /*  HISTORY                                                             */
    /* ------------------------------------------------------------------ */
    public function history(Patient $patient)
    {
        // Delegates to show() — provides a stable named route for history links
        return $this->show($patient);
    }

    /* ------------------------------------------------------------------ */
    /*  RESTORE  (MED-7 FIX)                                               */
    /* ------------------------------------------------------------------ */
    /**
     * Restore a soft-deleted patient record.
     * Route uses ->withTrashed() so the model binding resolves deleted records.
     * Gated on delete-patients permission (same trust level as deletion).
     */
    public function restore(Patient $patient)
    {
        $this->authorize('delete-patients');

        $patient->restore();

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', "Patient {$patient->full_name} has been restored.");
    }
}
