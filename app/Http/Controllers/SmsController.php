<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function __construct(private readonly SmsService $sms) {}

    /* ------------------------------------------------------------------ */
    /*  SMS LOG INDEX                                                        */
    /* ------------------------------------------------------------------ */
    public function index(Request $request)
    {
        $this->authorize('view-sms');

        $search   = $request->get('search', '');
        $status   = $request->get('status', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo   = $request->get('date_to', '');

        $logs = SmsLog::with('createdBy')
            ->when($search, fn ($q) => $q->where('recipient_number', 'like', "%{$search}%")
                ->orWhere('recipient_name', 'like', "%{$search}%"))
            ->when($status,   fn ($q) => $q->where('status', $status))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $filters = compact('search', 'status', 'dateFrom', 'dateTo');

        return view('sms.index', compact('logs', 'filters'));
    }

    /* ------------------------------------------------------------------ */
    /*  MANUAL SEND FORM                                                     */
    /* ------------------------------------------------------------------ */
    public function create()
    {
        $this->authorize('send-sms');
        return view('sms.create');
    }

    /* ------------------------------------------------------------------ */
    /*  SEND                                                                 */
    /* ------------------------------------------------------------------ */
    public function send(Request $request)
    {
        $this->authorize('send-sms');

        $validated = $request->validate([
            'recipient_number' => ['required', 'string', 'regex:/^09\d{9}$/', 'max:20'],
            'recipient_name'   => ['nullable', 'string', 'max:150'],
            'message'          => ['required', 'string', 'min:5', 'max:160'],
        ], [
            'recipient_number.regex' => 'Phone number must be a valid 11-digit Philippine mobile number (e.g. 09XXXXXXXXX).',
        ]);

        $log = $this->sms->send(
            number:        $validated['recipient_number'],
            message:       $validated['message'],
            recipientName: $validated['recipient_name'] ?? null,
        );

        $msg = $log->status === 'sent'
            ? 'SMS sent successfully.'
            : 'SMS queued but failed to deliver: ' . ($log->error_message ?? 'Unknown error.');

        $flashType = $log->status === 'sent' ? 'success' : 'warning';

        return redirect()->route('sms.index')->with($flashType, $msg);
    }
}
