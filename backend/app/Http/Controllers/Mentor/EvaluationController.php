<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\MentorEvaluation;
use App\Services\AuditService;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function store(Request $request, Application $application)
    {
        if ($application->mentor_user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'metrics' => 'required|array',
            'recommendation' => 'nullable|string',
            'final_status' => 'required|string|in:recommend,not_recommend',
        ]);

        $evaluation = MentorEvaluation::create([
            'application_id' => $application->id,
            'mentor_user_id' => auth()->id(),
            'title' => $validated['title'],
            'summary' => $validated['summary'],
            'metrics' => $validated['metrics'],
            'recommendation' => $validated['recommendation'],
            'final_status' => $validated['final_status'],
        ]);

        AuditService::log('mentor_evaluation_submitted', $evaluation, 'Final evaluation submitted for mentee: '.$application->user->name);

        return redirect()->back()->with('success', 'Evaluasi akhir berhasil dikirim.');
    }
}
