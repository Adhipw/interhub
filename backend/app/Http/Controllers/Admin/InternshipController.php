<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateInternshipStatusRequest;
use App\Models\Internship;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InternshipController extends Controller
{
    public function index(Request $request)
    {
        $internships = Internship::query()
            ->with('company')
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Internships/Index', [
            'internships' => $internships,
            'filters' => $request->only(['search']),
        ]);
    }

    public function updateStatus(UpdateInternshipStatusRequest $request, Internship $internship)
    {
        $validated = $request->validated();

        $oldStatus = $internship->status;
        $internship->status = $validated['status'];
        $internship->save();

        AuditService::log(
            'admin_internship_moderation',
            $internship,
            "Internship status changed from {$oldStatus} to {$validated['status']}"
        );

        return redirect()->back()->with('success', 'Status lowongan berhasil diperbarui.');
    }
}
