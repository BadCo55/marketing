<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeRouteRequest;
use App\Http\Requests\UpdateOfficeRouteRequest;
use App\Models\Office;
use App\Models\OfficeRoute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficeRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // CHANGED: Added eager loading and useful ordering for the route index.
        $routes = OfficeRoute::with([
            'assignedTo',
            'createdBy',
            'stops',
            'runs',
        ])
            ->orderByDesc('id')
            ->get();

        return inertia('OfficeRoute/Index', [
            'office_routes' => $routes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // CHANGED: Added office list for route planning.
        return inertia('OfficeRoute/Create', [
            'offices' => Office::orderBy('parent_company')
                ->orderBy('office_name')
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfficeRouteRequest $request)
    {
        // CHANGED: Pulled validated payload from the request.
        $validated = $request->validated();

        // CHANGED: Wrapped route + stop creation in a transaction.
        $officeRoute = DB::transaction(function () use ($validated) {
            $stops = $validated['stops'] ?? [];

            // CHANGED: Created the reusable route record.
            $officeRoute = OfficeRoute::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? 'draft',
                'created_by' => Auth::id(),
                'assigned_to' => $validated['assigned_to'] ?? null,
                'route_date' => $validated['route_date'] ?? null,
                'center_latitude' => $validated['center_latitude'] ?? null,
                'center_longitude' => $validated['center_longitude'] ?? null,
                'radius_miles' => $validated['radius_miles'] ?? null,
                'total_stops' => count($stops),
                'estimated_total_drive_minutes' => $validated['estimated_total_drive_minutes'] ?? null,
                'estimated_total_drive_miles' => $validated['estimated_total_drive_miles'] ?? null,
                'source_type' => $validated['source_type'] ?? null,
                'import_file_name' => $validated['import_file_name'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // CHANGED: Created each planning stop from the submitted route builder payload.
            foreach ($stops as $index => $stop) {
                $officeRoute->stops()->create([
                    'office_id' => $stop['office_id'] ?? null,
                    'stop_order' => $stop['stop_order'] ?? ($index + 1),
                    'parent_company' => $stop['parent_company'] ?? null,
                    'office_name' => $stop['office_name'] ?? null,
                    'street_address' => $stop['street_address'] ?? null,
                    'unit_number' => $stop['unit_number'] ?? null,
                    'city' => $stop['city'] ?? null,
                    'state' => $stop['state'] ?? null,
                    'zip_code' => $stop['zip_code'] ?? null,
                    'latitude' => $stop['latitude'] ?? null,
                    'longitude' => $stop['longitude'] ?? null,
                    'office_phone' => $stop['office_phone'] ?? null,
                    'estimated_drive_minutes_from_previous' => $stop['estimated_drive_minutes_from_previous'] ?? null,
                    'estimated_drive_miles_from_previous' => $stop['estimated_drive_miles_from_previous'] ?? null,
                    'priority' => $stop['priority'] ?? 3,
                    'required' => $stop['required'] ?? false,
                    'notes' => $stop['notes'] ?? null,
                ]);
            }

            // CHANGED: Returned the fresh route with relationships used on the show page.
            return $officeRoute->load([
                'assignedTo',
                'createdBy',
                'stops.office',
                'runs',
            ]);
        });

        return redirect()
            ->route('office-route.show', $officeRoute)
            ->with('success', 'Office route created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OfficeRoute $officeRoute)
    {
        // CHANGED: Loaded all route relationships needed for the show page.
        $officeRoute->load([
            'assignedTo',
            'createdBy',
            'stops.office',
            'runs.assignedTo',
            'runs.startedBy',
            'runs.completedBy',
        ]);

        return inertia('OfficeRoute/Show', [
            'office_route' => $officeRoute,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfficeRoute $officeRoute)
    {
        // CHANGED: Loaded stops for editing and offices for stop selection.
        $officeRoute->load([
            'stops.office',
        ]);

        return inertia('OfficeRoute/Edit', [
            'office_route' => $officeRoute,
            'offices' => Office::orderBy('parent_company')
                ->orderBy('office_name')
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfficeRouteRequest $request, OfficeRoute $officeRoute)
    {
        // CHANGED: Pulled validated data for updating the reusable route.
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $officeRoute) {
            $stops = $validated['stops'] ?? [];

            // CHANGED: Updated the main route record.
            $officeRoute->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? $officeRoute->status,
                'assigned_to' => $validated['assigned_to'] ?? null,
                'route_date' => $validated['route_date'] ?? null,
                'center_latitude' => $validated['center_latitude'] ?? null,
                'center_longitude' => $validated['center_longitude'] ?? null,
                'radius_miles' => $validated['radius_miles'] ?? null,
                'total_stops' => count($stops),
                'estimated_total_drive_minutes' => $validated['estimated_total_drive_minutes'] ?? null,
                'estimated_total_drive_miles' => $validated['estimated_total_drive_miles'] ?? null,
                'source_type' => $validated['source_type'] ?? null,
                'import_file_name' => $validated['import_file_name'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // CHANGED: Rebuilt route stops from the incoming ordered payload.
            $officeRoute->stops()->delete();

            foreach ($stops as $index => $stop) {
                $officeRoute->stops()->create([
                    'office_id' => $stop['office_id'] ?? null,
                    'stop_order' => $stop['stop_order'] ?? ($index + 1),
                    'parent_company' => $stop['parent_company'] ?? null,
                    'office_name' => $stop['office_name'] ?? null,
                    'street_address' => $stop['street_address'] ?? null,
                    'unit_number' => $stop['unit_number'] ?? null,
                    'city' => $stop['city'] ?? null,
                    'state' => $stop['state'] ?? null,
                    'zip_code' => $stop['zip_code'] ?? null,
                    'latitude' => $stop['latitude'] ?? null,
                    'longitude' => $stop['longitude'] ?? null,
                    'office_phone' => $stop['office_phone'] ?? null,
                    'estimated_drive_minutes_from_previous' => $stop['estimated_drive_minutes_from_previous'] ?? null,
                    'estimated_drive_miles_from_previous' => $stop['estimated_drive_miles_from_previous'] ?? null,
                    'priority' => $stop['priority'] ?? 3,
                    'required' => $stop['required'] ?? false,
                    'notes' => $stop['notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('office-route.show', $officeRoute)
            ->with('success', 'Office route updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfficeRoute $officeRoute)
    {
        // CHANGED: Soft-deleted the route record.
        $officeRoute->delete();

        return redirect()
            ->route('office-route.index')
            ->with('success', 'Office route deleted successfully.');
    }
}
