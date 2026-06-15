<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLocationRequest;
use App\Models\Location;
use App\Services\AuditService;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::latest()->paginate(10);

        return Inertia::render('Admin/Locations/Index', [
            'locations' => $locations,
        ]);
    }

    public function store(StoreLocationRequest $request)
    {
        $validated = $request->validated();

        $location = Location::create($validated);

        AuditService::log('admin_location_created', $location, "Master data location created: {$location->name}");

        return redirect()->back()->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function toggleStatus(Location $location)
    {
        $location->is_active = ! $location->is_active;
        $location->save();

        AuditService::log('admin_location_moderation', $location, "Location status toggled: {$location->name}");

        return redirect()->back()->with('success', 'Status lokasi diperbarui.');
    }

    public function destroy(Location $location)
    {
        $locationName = $location->name;
        $location->delete();

        AuditService::log('admin_location_deleted', null, "Location deleted: {$locationName}");

        return redirect()->back()->with('success', 'Lokasi berhasil dihapus.');
    }
}
