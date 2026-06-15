<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationMessageController extends Controller
{
    public function index(Application $application)
    {
        $this->authorize('view', $application);

        return response()->json([
            'success' => true,
            'data' => $application->messages()->with('user')->oldest()->get(),
        ]);
    }

    public function store(Request $request, Application $application)
    {
        $this->authorize('view', $application);

        $request->validate([
            'message' => 'required|string|max:1000',
            'attachments' => 'nullable|array',
        ]);

        $message = $application->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachments' => $request->attachments,
        ]);

        return response()->json([
            'success' => true,
            'data' => $message->load('user'),
        ]);
    }
}
