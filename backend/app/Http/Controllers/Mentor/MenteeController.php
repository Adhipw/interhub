<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\MentorFeedback;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenteeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $company = app('current_company');

        $mentees = Application::where('mentor_user_id', $user->id)
            ->whereHas('internship', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->with(['user', 'internship', 'mentorFeedbacks'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Mentor/Mentees/Index', [
            'mentees' => $mentees,
        ]);
    }

    public function show(Application $application)
    {
        // Ensure the mentor is assigned to this mentee
        if ($application->mentor_user_id !== auth()->id()) {
            abort(403, 'Anda tidak ditugaskan untuk mengelola kandidat ini.');
        }

        $application->load([
            'user.detail',
            'internship',
            'mentorFeedbacks.mentor',
            'tasks',
            'evaluations',
            'mentoringSessions',
        ]);

        return Inertia::render('Mentor/Mentees/Show', [
            'application' => $application,
            'feedbacks' => $application->mentorFeedbacks,
            'tasks' => $application->tasks,
            'evaluations' => $application->evaluations,
            'sessions' => $application->mentoringSessions,
        ]);
    }

    public function storeFeedback(Request $request, Application $application)
    {
        if ($application->mentor_user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:10',
            'assessment' => 'nullable|array',
            'assessment.technical' => 'nullable|integer|min:1|max:5',
            'assessment.soft_skills' => 'nullable|integer|min:1|max:5',
            'assessment.attitude' => 'nullable|integer|min:1|max:5',
        ]);

        $feedback = MentorFeedback::create([
            'application_id' => $application->id,
            'mentor_user_id' => auth()->id(),
            'content' => $validated['content'],
            'assessment' => $validated['assessment'] ?? [],
            'status' => 'submitted',
        ]);

        AuditService::log('mentor_feedback_submitted', $feedback, 'Feedback submitted for mentee: '.$application->user->name);

        return redirect()->back()->with('success', 'Feedback berhasil dikirim.');
    }
}
