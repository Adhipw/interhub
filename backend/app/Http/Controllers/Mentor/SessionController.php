<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\MentoringSession;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function store(Request $request, Application $application)
    {
        if ($application->mentor_user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15',
            'meeting_link' => 'nullable|url',
        ]);

        $session = MentoringSession::create([
            'application_id' => $application->id,
            'mentor_user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'],
            'meeting_link' => $validated['meeting_link'],
            'status' => 'planned',
        ]);

        AuditService::log('mentor_session_created', $session, 'Mentoring session scheduled for mentee: '.$application->user->name);

        return redirect()->back()->with('success', 'Sesi mentoring berhasil dijadwalkan.');
    }

    public function updateStatus(Request $request, MentoringSession $session)
    {
        if ($session->mentor_user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:planned,completed,cancelled',
        ]);

        $oldStatus = $session->status;
        $session->update(['status' => $validated['status']]);

        AuditService::log('mentor_session_status_updated', $session, "Mentoring session status changed from {$oldStatus} to {$validated['status']}");

        return redirect()->back()->with('success', 'Status sesi mentoring berhasil diperbarui.');
    }
}
