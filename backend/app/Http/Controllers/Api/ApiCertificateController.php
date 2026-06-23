<?php

namespace App\Http\Controllers\Api;

use App\Models\Application;
use App\Models\MentorEvaluation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiCertificateController extends ApiBaseController
{
    /**
     * Get certificate data, render HTML, or download PDF.
     */
    public function show(Request $request, Application $application)
    {
        /** @var User $user */
        $user = Auth::user();

        // Security check: Only the student or company staff can view
        $isStudent = $application->user_id === $user->id;
        $isCompanyStaff = $user->companies()->where('companies.id', $application->internship->company_id)->exists();
        $isAdmin = $user->hasRole('admin') || $user->hasRole('super_admin');

        if (! $isStudent && ! $isCompanyStaff && ! $isAdmin) {
            return $this->sendError(__('Unauthorized access to this certificate.'), [], 403);
        }

        // Check if internship is completed
        if ($application->status !== 'completed') {
            return $this->sendError(__('Certificate is not available yet. The internship must be completed first.'), [], 422);
        }

        $evaluation = MentorEvaluation::where('application_id', $application->id)->first();

        $data = [
            'certificate_id' => 'IH-' . strtoupper(substr(md5($application->id . $application->created_at), 0, 8)),
            'student_name' => $application->user->name,
            'internship_title' => $application->internship->title,
            'company_name' => $application->internship->company->name,
            'company_logo' => $application->internship->company->logo_url,
            'mentor_name' => $application->mentor->name ?? 'Company Representative',
            'start_date' => $application->created_at->format('M d, Y'),
            'end_date' => $evaluation ? $evaluation->created_at->format('M d, Y') : now()->format('M d, Y'),
        ];

        // 1. If PDF download is requested
        if ($request->has('download')) {
            if (! class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                return $this->sendError(__('PDF library not installed. Please contact administrator.'), [], 500);
            }

            $pdf = Pdf::loadView('certificates.internship', $data)
                ->setPaper('a4', 'landscape')
                ->setWarnings(false);

            return $pdf->download("Certificate_{$data['certificate_id']}.pdf");
        }

        // 2. If request wants JSON (data)
        if ($request->wantsJson()) {
            return $this->sendResponse($data, 'Certificate data retrieved successfully');
        }

        // 3. Otherwise render HTML (for printing/viewing)
        return view('certificates.internship', $data);
    }
}
