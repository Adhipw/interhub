<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\AssignMentorRequest;
use App\Http\Requests\HR\ScheduleInterviewRequest;
use App\Http\Requests\HR\UpdateApplicationStatusRequest;
use App\Models\Application;
use App\Models\CompanyMember;
use App\Models\InterviewSchedule;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $company = app('current_company');

        // Scope all queries to current company unless Super Admin
        $query = Application::with(['user', 'internship'])
            ->whereHas('internship', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            });

        $applications = $query->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->internship_id, fn ($q) => $q->where('internship_id', $request->internship_id))
            ->latest()
            ->paginate(15);

        return Inertia::render('HR/Applications/Index', [
            'applications' => $applications,
            'filters' => $request->only(['status', 'internship_id']),
        ]);
    }

    public function show(Application $application)
    {
        $this->authorize('view', $application);

        $company = app('current_company');

        // Get mentors from the same company
        $mentors = CompanyMember::where('company_id', $company->id)
            ->where('role', 'mentor')
            ->where('is_active', true)
            ->with('user')
            ->get();

        return Inertia::render('HR/Applications/Show', [
            'application' => $application->load(['user.detail', 'internship', 'interviewSchedules.interviewer', 'interviewer', 'mentor']),
            'mentors' => $mentors,
        ]);
    }

    public function updateStatus(UpdateApplicationStatusRequest $request, Application $application)
    {
        $this->authorize('update', $application);

        $oldStatus = $application->status;

        DB::transaction(function () use ($application, $request, $oldStatus) {
            $application->update([
                'status' => $request->status,
                'hr_notes' => $request->notes,
                'timeline' => array_merge($application->timeline ?? [], [[
                    'status' => $request->status,
                    'label' => match ($request->status) {
                        'accepted' => 'Selamat! Lamaran Anda Diterima',
                        'rejected' => 'Update Status Lamaran',
                        'reviewing' => 'Lamaran Sedang Ditinjau',
                        default => 'Status Lamaran Diperbarui'
                    },
                    'description' => match ($request->status) {
                        'accepted' => 'Kami dengan senang hati mengundang Anda untuk bergabung bersama kami. Tim kami akan segera menghubungi Anda untuk langkah selanjutnya.',
                        'rejected' => 'Terima kasih telah melamar. Saat ini kami memutuskan untuk belum melanjutkan proses lamaran Anda. Tetap semangat!',
                        'reviewing' => 'Kandidat sedang masuk dalam tahap tinjauan mendalam oleh tim HR kami.',
                        default => $request->notes ?: "Status lamaran Anda telah diperbarui menjadi {$request->status}."
                    },
                    'date' => now()->toDateTimeString(),
                    'by' => Auth::user()->name,
                ]]),
            ]);

            // Audit Log Mandatory
            AuditService::log('application_status_updated', $application, [
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'notes' => $request->notes,
            ]);
        });

        return back()->with('status', 'Status lamaran berhasil diperbarui.');
    }

    public function scheduleInterview(ScheduleInterviewRequest $request, Application $application)
    {
        $this->authorize('update', $application);

        DB::transaction(function () use ($application, $request) {
            InterviewSchedule::create([
                'application_id' => $application->id,
                'interviewer_id' => Auth::id(),
                'scheduled_at' => $request->scheduled_at,
                'type' => $request->type,
                'meeting_link' => $request->meeting_link,
                'location' => $request->location,
                'notes' => $request->notes,
            ]);

            if ($application->status === 'pending') {
                $application->update(['status' => 'reviewing']);
            }

            $application->update([
                'timeline' => array_merge($application->timeline ?? [], [[
                    'status' => 'interview_scheduled',
                    'label' => 'Wawancara Dijadwalkan',
                    'description' => 'Wawancara telah dijadwalkan pada '.Carbon::parse($request->scheduled_at)->format('d M Y, H:i'),
                    'date' => now()->toDateTimeString(),
                    'by' => Auth::user()->name,
                ]]),
            ]);

            AuditService::log('interview_scheduled', $application, null, $request->validated());
        });

        return back()->with('status', 'Jadwal wawancara berhasil dibuat.');
    }

    public function assignMentor(AssignMentorRequest $request, Application $application)
    {
        $this->authorize('update', $application);

        // Validation already checked if mentor belongs to company in AssignMentorRequest

        $application->update([
            'mentor_user_id' => $request->mentor_user_id,
            'timeline' => array_merge($application->timeline ?? [], [[
                'status' => 'mentor_assigned',
                'label' => 'Mentor Ditugaskan',
                'description' => 'Seorang mentor telah ditugaskan untuk membimbing Anda.',
                'date' => now()->toDateTimeString(),
                'by' => Auth::user()->name,
            ]]),
        ]);

        AuditService::log('mentor_assigned', $application, null, ['mentor_user_id' => $request->mentor_user_id]);

        return back()->with('status', 'Mentor berhasil ditugaskan.');
    }
}
