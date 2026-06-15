<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class ApiAdminLocationController extends Controller
{
    public function index()
    {
        $locations = Location::latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $locations,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:locations,name',
            'type' => 'required|string|in:city,province,region',
            'is_active' => 'boolean',
        ]);

        $location = Location::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Location created',
            'data' => $location,
        ]);
    }

    public function toggle(Location $location)
    {
        $location->is_active = ! $location->is_active;
        $location->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Location status updated',
            'data' => $location,
        ]);
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Location deleted',
        ]);
    }
}
