<?php

namespace App\Http\Controllers;

use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentTimeSlot;
use App\Models\Patient;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentService $service) {}

    /* ------------------------------------------------------------------ */
    /*  INDEX                                                               */
    /* ------------------------------------------------------------------ */
    public function index(Request $request)
    {
        $this->authorize('view-appointments');

        $status = $request->get('status', '');
        $date   = $request->get('date', '');
        $search = $request->get('search', '');

        $appointments = Appointment::with('patient', 'createdBy', 'approvedBy')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($date,   fn ($q) => $q->whereDate('appointment_date', $date))
            ->when($search, fn ($q) => $q->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('patient_number', 'like', "%{$search}%");
            }))
            ->orderByDesc('appointment_date')
            ->orderBy('appointment_time')
            ->paginate(20)
            ->withQueryString();

        $counts = Appointment::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('appointments.index', [
            'appointments'  => $appointments,
            'statusLabels'  => Appointment::statusLabels(),
            'counts'        => $counts,
            'filters'       => compact('status', 'date', 'search'),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  CREATE / STORE                                                      */
    /* ------------------------------------------------------------------ */
    public function create(Request $request)
    {
        $this->authorize('create-appointments');

        $patients  = Patient::active()->orderBy('last_name')->orderBy('first_name')->get();
        $timeSlots = AppointmentTimeSlot::active()->get();
        $selected  = $request->integer('patient_id') ?: null;

        return view('appointments.create', compact('patients', 'timeSlots', 'selected'));
    }

    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();

        $remaining = $this->service->checkSlotAvailability(
            $data['appointment_date'],
            $data['appointment_time']
        );

        if ($remaining === 0) {
            return back()
                ->withInput()
                ->withErrors(['appointment_time' => 'This time slot is fully booked. Please choose another time.']);
        }

        $data['created_by'] = auth()->id();
        $data['status']     = 'pending';

        $appointment = Appointment::create($data);

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', 'Appointment booked successfully and is pending approval.');
    }

    /* ------------------------------------------------------------------ */
    /*  SHOW                                                                */
    /* ------------------------------------------------------------------ */
    public function show(Appointment $appointment)
    {
        $this->authorize('view-appointments');
        $appointment->load('patient', 'approvedBy', 'createdBy', 'consultation');

        return view('appointments.show', compact('appointment'));
    }

    /* ------------------------------------------------------------------ */
    /*  EDIT / UPDATE                                                       */
    /* ------------------------------------------------------------------ */
    public function edit(Appointment $appointment)
    {
        $this->authorize('update-appointments');

        $patients  = Patient::active()->orderBy('last_name')->orderBy('first_name')->get();
        $timeSlots = AppointmentTimeSlot::active()->get();

        return view('appointments.edit', compact('appointment', 'patients', 'timeSlots'));
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $this->authorize('update-appointments');

        $data = $request->validated();

        $remaining = $this->service->checkSlotAvailability(
            $data['appointment_date'],
            $data['appointment_time'],
            $appointment->id
        );

        if ($remaining === 0) {
            return back()
                ->withInput()
                ->withErrors(['appointment_time' => 'This time slot is fully booked. Please choose another time.']);
        }

        $appointment->update($data);

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    /* ------------------------------------------------------------------ */
    /*  DESTROY                                                             */
    /* ------------------------------------------------------------------ */
    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete-appointments');
        $appointment->delete();

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment deleted.');
    }

    /* ------------------------------------------------------------------ */
    /*  STATUS TRANSITIONS                                                  */
    /* ------------------------------------------------------------------ */
    public function approve(Request $request, Appointment $appointment)
    {
        $this->authorize('approve-appointments');
        $this->service->approve($appointment);

        return back()->with('success', 'Appointment approved successfully.');
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        $this->authorize('approve-appointments');

        $request->validate([
            'cancelled_reason' => ['required', 'string', 'max:500'],
        ]);

        $this->service->cancel($appointment, $request->cancelled_reason);

        return back()->with('success', 'Appointment cancelled.');
    }

    public function noShow(Appointment $appointment)
    {
        $this->authorize('update-appointments');
        $this->service->markNoShow($appointment);

        return back()->with('success', 'Appointment marked as No Show.');
    }

    public function complete(Appointment $appointment)
    {
        $this->authorize('update-appointments');
        $this->service->markCompleted($appointment);

        return back()->with('success', 'Appointment marked as Completed.');
    }
}
