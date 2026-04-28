<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->latest('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('type')) {
            $query->where('auditable_type', 'like', '%' . $request->type . '%');
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('q')) {
            $query->where('description', 'like', '%' . $request->q . '%');
        }

        $logs  = $query->paginate(40)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);

        $actions = ['created','updated','deleted','viewed','login','logout','failed_login','export'];

        return view('admin.audit-logs.index', compact('logs', 'users', 'actions'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user', 'auditable']);
        return view('admin.audit-logs.show', ['log' => $auditLog]);
    }
}
