<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SavedInternshipController extends Controller
{
    public function index()
    {
        return Inertia::render('Internships/Saved', [
            'savedInternships' => Auth::user()->savedInternships()
                ->with('internship.company')
                ->latest()
                ->get(),
        ]);
    }

    public function toggle(Internship $internship)
    {
        $user = Auth::user();
        $saved = $user->savedInternships()->where('internship_id', $internship->id)->first();

        if ($saved) {
            $saved->delete();

            return back()->with('status', 'unsaved');
        }

        $user->savedInternships()->create(['internship_id' => $internship->id]);

        return back()->with('status', 'saved');
    }
}
