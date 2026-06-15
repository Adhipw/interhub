<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SecurityEventController extends Controller
{
    public function index(Request $request)
    {
        // Admin only sees limited security events (low and medium severity)
        // Critical and Fatal events are Super Admin only
        $events = SecurityEvent::query()
            ->with('user')
            ->whereIn('severity', ['low', 'medium'])
            ->when($request->search, function ($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhere('event_type', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Security/Index', [
            'events' => $events,
            'filters' => $request->only(['search']),
        ]);
    }
}
