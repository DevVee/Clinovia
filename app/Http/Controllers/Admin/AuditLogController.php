<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-audit-logs');

        $logs = AuditLog::with('user')
            ->when($request->search,    fn ($q) => $q->where('description', 'like', "%{$request->search}%"))
            ->when($request->module,    fn ($q) => $q->where('module', $request->module))
            ->when($request->action,    fn ($q) => $q->where('action', $request->action))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }
}
