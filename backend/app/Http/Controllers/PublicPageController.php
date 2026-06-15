<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicPageController extends Controller
{
    public function help(Request $request, $section = 'center')
    {
        return Inertia::render('Public/InfoPage', [
            'initialSection' => $section,
        ]);
    }

    public function terms()
    {
        return Inertia::render('Public/InfoPage', ['initialSection' => 'terms']);
    }

    public function privacy()
    {
        return Inertia::render('Public/InfoPage', ['initialSection' => 'privacy']);
    }

    public function contact()
    {
        return Inertia::render('Public/InfoPage', ['initialSection' => 'contact']);
    }

    public function careerTips()
    {
        return Inertia::render('Public/InfoPage', ['initialSection' => 'career-tips']);
    }

    public function cvGuide()
    {
        return Inertia::render('Public/InfoPage', ['initialSection' => 'cv-guide']);
    }

    public function campusProgram()
    {
        return Inertia::render('Public/InfoPage', ['initialSection' => 'campus-program']);
    }

    public function selectionSystem()
    {
        return Inertia::render('Public/InfoPage', ['initialSection' => 'selection-system']);
    }

    public function employerBranding()
    {
        return Inertia::render('Public/InfoPage', ['initialSection' => 'employer-branding']);
    }

    public function enterprise()
    {
        return Inertia::render('Public/InfoPage', ['initialSection' => 'enterprise']);
    }
}
