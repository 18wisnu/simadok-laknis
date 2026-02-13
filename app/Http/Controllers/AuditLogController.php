<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.audit_logs', compact('logs'));
    }
}
