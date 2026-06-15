<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationStageHistory;
use App\Models\Internship;
use App\Models\RecruitmentStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PipelineController extends Controller
{
    public function getKanbanData(Internship $internship)
    {
        $companyId = request()->header('X-Company-Id') ?? session('current_company_id');

        if ($internship->company_id != $companyId) {
            abort(403);
        }

        $stages = RecruitmentStage::where('internship_id', $internship->id)
            ->orderBy('order')
            ->with(['applications.user.detail', 'applications.score'])
            ->get();

        return response()->json([
            'stages' => $stages,
        ]);
    }

    public function updateStage(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'to_stage_id' => 'required|exists:recruitment_stages,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $app = Application::with('internship')->findOrFail($request->application_id);

        $companyId = $request->header('X-Company-Id') ?? session('current_company_id');

        if ($app->internship->company_id != $companyId) {
            return response()->json(['error' => 'Unauthorized company access.'], 403);
        }

        $toStage = RecruitmentStage::findOrFail($request->to_stage_id);
        if ($toStage->internship_id !== $app->internship_id) {
            return response()->json(['error' => 'Stage tidak valid untuk lowongan ini.'], 422);
        }

        $oldStageId = $app->current_stage_id;

        if ($oldStageId == $request->to_stage_id) {
            return response()->json(['message' => 'Sudah berada di stage ini.'], 422);
        }

        $app->current_stage_id = $request->to_stage_id;

        if ($toStage->type === 'hired') {
            $app->status = 'hired';
        } elseif ($toStage->type === 'rejected') {
            $app->status = 'rejected';
        } else {
            $app->status = 'in_review';
        }

        $app->save();

        $lastTransition = ApplicationStageHistory::where('application_id', $app->id)
            ->latest()
            ->first();

        $duration = $lastTransition ? now()->diffInMinutes($lastTransition->created_at) : null;

        ApplicationStageHistory::create([
            'application_id' => $app->id,
            'from_stage_id' => $oldStageId,
            'to_stage_id' => $request->to_stage_id,
            'changed_by' => Auth::id() ?? 1, // Fallback for testing
            'notes' => $request->notes,
            'duration_minutes' => $duration,
        ]);

        return response()->json([
            'message' => 'Stage berhasil diperbarui.',
            'current_status' => $app->status,
        ]);
    }

    public function getHistory(Application $application)
    {
        $companyId = request()->header('X-Company-Id') ?? session('current_company_id');
        if ($application->internship->company_id != $companyId) {
            abort(403);
        }

        $history = ApplicationStageHistory::where('application_id', $application->id)
            ->with(['fromStage', 'toStage', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }
}
