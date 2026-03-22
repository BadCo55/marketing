<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeRouteStopRequest;
use App\Http\Requests\UpdateOfficeRouteStopRequest;
use App\Models\OfficeRouteStop;

class OfficeRouteStopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // CHANGED: Stops are typically managed through the route screens.
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // CHANGED: Route stops are usually created from the route builder UI.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfficeRouteStopRequest $request)
    {
        // CHANGED: Added standalone route-stop creation aligned to the planning-only stop model.
        $validated = $request->validated();

        $officeRouteStop = OfficeRouteStop::create([
            'office_route_id' => $validated['office_route_id'],
            'office_id' => $validated['office_id'] ?? null,
            'stop_order' => $validated['stop_order'],
            'parent_company' => $validated['parent_company'] ?? null,
            'office_name' => $validated['office_name'] ?? null,
            'street_address' => $validated['street_address'] ?? null,
            'unit_number' => $validated['unit_number'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'zip_code' => $validated['zip_code'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'office_phone' => $validated['office_phone'] ?? null,
            'estimated_drive_minutes_from_previous' => $validated['estimated_drive_minutes_from_previous'] ?? null,
            'estimated_drive_miles_from_previous' => $validated['estimated_drive_miles_from_previous'] ?? null,
            'priority' => $validated['priority'] ?? 3,
            'required' => $validated['required'] ?? false,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('office-route.show', $officeRouteStop->office_route_id)
            ->with('success', 'Route stop created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OfficeRouteStop $officeRouteStop)
    {
        // CHANGED: Standalone stop show page not currently used.
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfficeRouteStop $officeRouteStop)
    {
        // CHANGED: Standalone stop edit page not currently used.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfficeRouteStopRequest $request, OfficeRouteStop $officeRouteStop)
    {
        // CHANGED: Added standalone planning-stop update support.
        $validated = $request->validated();

        $officeRouteStop->update([
            'office_id' => $validated['office_id'] ?? $officeRouteStop->office_id,
            'stop_order' => $validated['stop_order'] ?? $officeRouteStop->stop_order,
            'parent_company' => $validated['parent_company'] ?? $officeRouteStop->parent_company,
            'office_name' => $validated['office_name'] ?? $officeRouteStop->office_name,
            'street_address' => $validated['street_address'] ?? $officeRouteStop->street_address,
            'unit_number' => $validated['unit_number'] ?? $officeRouteStop->unit_number,
            'city' => $validated['city'] ?? $officeRouteStop->city,
            'state' => $validated['state'] ?? $officeRouteStop->state,
            'zip_code' => $validated['zip_code'] ?? $officeRouteStop->zip_code,
            'latitude' => $validated['latitude'] ?? $officeRouteStop->latitude,
            'longitude' => $validated['longitude'] ?? $officeRouteStop->longitude,
            'office_phone' => $validated['office_phone'] ?? $officeRouteStop->office_phone,
            'estimated_drive_minutes_from_previous' => $validated['estimated_drive_minutes_from_previous'] ?? $officeRouteStop->estimated_drive_minutes_from_previous,
            'estimated_drive_miles_from_previous' => $validated['estimated_drive_miles_from_previous'] ?? $officeRouteStop->estimated_drive_miles_from_previous,
            'priority' => $validated['priority'] ?? $officeRouteStop->priority,
            'required' => $validated['required'] ?? $officeRouteStop->required,
            'notes' => $validated['notes'] ?? $officeRouteStop->notes,
        ]);

        return redirect()
            ->route('office-route.show', $officeRouteStop->office_route_id)
            ->with('success', 'Route stop updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfficeRouteStop $officeRouteStop)
    {
        // CHANGED: Captured route id before delete so redirect remains valid.
        $officeRouteId = $officeRouteStop->office_route_id;

        $officeRouteStop->delete();

        return redirect()
            ->route('office-route.show', $officeRouteId)
            ->with('success', 'Route stop removed successfully.');
    }
}
