<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/AuditLogs/Index', [
            'logs' => AuditLog::with('user:id,name')->latest()->paginate(30),
        ]);
    }
}
