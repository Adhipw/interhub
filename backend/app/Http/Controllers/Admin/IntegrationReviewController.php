<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IntegrationReviewController extends Controller
{
    /**
     * List all external listings pending review.
     */
    public function index()
    {
        $listings = Internship::where('is_external', true)
            ->where('status', 'pending_review')
            ->with('company')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Reports/ExternalListings', [
            'listings' => $listings,
        ]);
    }

    /**
     * Approve an external listing.
     */
    public function approve(Internship $internship)
    {
        if (! $internship->is_external) {
            return response()->json(['message' => 'Not an external listing.'], 400);
        }

        $internship->update([
            'status' => 'published',
        ]);

        AuditService::log('admin_external_listing_approved', $internship, "External listing approved: {$internship->title}");

        return redirect()->back()->with('success', 'Listing approved and published.');
    }

    /**
     * Reject/Delete an external listing.
     */
    public function reject(Internship $internship)
    {
        if (! $internship->is_external) {
            return response()->json(['message' => 'Not an external listing.'], 400);
        }

        $title = $internship->title;
        $internship->delete();

        AuditService::log('admin_external_listing_rejected', null, "External listing rejected: {$title}");

        return redirect()->back()->with('success', 'Listing rejected and removed.');
    }

    /**
     * Bulk approve.
     */
    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids', []);
        Internship::whereIn('id', $ids)
            ->where('is_external', true)
            ->update(['status' => 'published']);

        return response()->json(['message' => 'Selected listings approved.']);
    }
}
