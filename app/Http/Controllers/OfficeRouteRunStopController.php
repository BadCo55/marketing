<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeRouteRunStopRequest;
use App\Http\Requests\UpdateOfficeRouteRunStopRequest;
use App\Models\OfficeRouteRunStop;
use Illuminate\Support\Facades\Auth;

class OfficeRouteRunStopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // CHANGED: Run stops are typically managed from the route run screen.
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // CHANGED: Run stops are usually created automatically when a run begins.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfficeRouteRunStopRequest $request)
    {
        // CHANGED: Added standalone run-stop creation support if needed later.
        $validated = $request->validated();

        $officeRunStop = OfficeRouteRunStop::create([
            'office_route_run_id' => $validated['office_route_run_id'],
            'office_route_stop_id' => $validated['office_route_stop_id'] ?? null,
            'office_id' => $validated['office_id'] ?? null,
            'planned_stop_order' => $validated['planned_stop_order'] ?? null,
            'actual_stop_order' => $validated['actual_stop_order'] ?? null,
            'status' => $validated['status'] ?? 'pending',
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
            'priority' => $validated['priority'] ?? 3,
            'required' => $validated['required'] ?? false,
            'estimated_drive_minutes_from_previous' => $validated['estimated_drive_minutes_from_previous'] ?? null,
            'estimated_drive_miles_from_previous' => $validated['estimated_drive_miles_from_previous'] ?? null,
            'actual_drive_minutes_from_previous' => $validated['actual_drive_minutes_from_previous'] ?? null,
            'actual_drive_miles_from_previous' => $validated['actual_drive_miles_from_previous'] ?? null,
            'started_at' => $validated['started_at'] ?? null,
            'arrived_at' => $validated['arrived_at'] ?? null,
            'completed_at' => $validated['completed_at'] ?? null,
            'skipped_at' => $validated['skipped_at'] ?? null,
            'cancelled_at' => $validated['cancelled_at'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'office_visit_id' => $validated['office_visit_id'] ?? null,
            'outcome' => $validated['outcome'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'skip_reason' => $validated['skip_reason'] ?? null,
            'cancel_reason' => $validated['cancel_reason'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()
            ->with('success', 'Run stop created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OfficeRouteRunStop $officeRunStop)
    {
        // CHANGED: Standalone run-stop show page not currently used.
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfficeRouteRunStop $officeRunStop)
    {
        // CHANGED: Standalone run-stop edit page not currently used.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfficeRouteRunStopRequest $request, OfficeRouteRunStop $officeRunStop)
    {
        // CHANGED: Added run-stop update support for execution changes.
        $validated = $request->validated();

        $officeRunStop->update([
            'office_route_stop_id' => $validated['office_route_stop_id'] ?? $officeRunStop->office_route_stop_id,
            'office_id' => $validated['office_id'] ?? $officeRunStop->office_id,
            'planned_stop_order' => $validated['planned_stop_order'] ?? $officeRunStop->planned_stop_order,
            'actual_stop_order' => $validated['actual_stop_order'] ?? $officeRunStop->actual_stop_order,
            'status' => $validated['status'] ?? $officeRunStop->status,
            'parent_company' => $validated['parent_company'] ?? $officeRunStop->parent_company,
            'office_name' => $validated['office_name'] ?? $officeRunStop->office_name,
            'street_address' => $validated['street_address'] ?? $officeRunStop->street_address,
            'unit_number' => $validated['unit_number'] ?? $officeRunStop->unit_number,
            'city' => $validated['city'] ?? $officeRunStop->city,
            'state' => $validated['state'] ?? $officeRunStop->state,
            'zip_code' => $validated['zip_code'] ?? $officeRunStop->zip_code,
            'latitude' => $validated['latitude'] ?? $officeRunStop->latitude,
            'longitude' => $validated['longitude'] ?? $officeRunStop->longitude,
            'office_phone' => $validated['office_phone'] ?? $officeRunStop->office_phone,
            'priority' => $validated['priority'] ?? $officeRunStop->priority,
            'required' => $validated['required'] ?? $officeRunStop->required,
            'estimated_drive_minutes_from_previous' => $validated['estimated_drive_minutes_from_previous'] ?? $officeRunStop->estimated_drive_minutes_from_previous,
            'estimated_drive_miles_from_previous' => $validated['estimated_drive_miles_from_previous'] ?? $officeRunStop->estimated_drive_miles_from_previous,
            'actual_drive_minutes_from_previous' => $validated['actual_drive_minutes_from_previous'] ?? $officeRunStop->actual_drive_minutes_from_previous,
            'actual_drive_miles_from_previous' => $validated['actual_drive_miles_from_previous'] ?? $officeRunStop->actual_drive_miles_from_previous,
            'started_at' => $validated['started_at'] ?? $officeRunStop->started_at,
            'arrived_at' => $validated['arrived_at'] ?? $officeRunStop->arrived_at,
            'completed_at' => $validated['completed_at'] ?? $officeRunStop->completed_at,
            'skipped_at' => $validated['skipped_at'] ?? $officeRunStop->skipped_at,
            'cancelled_at' => $validated['cancelled_at'] ?? $officeRunStop->cancelled_at,
            'duration_minutes' => $validated['duration_minutes'] ?? $officeRunStop->duration_minutes,
            'office_visit_id' => $validated['office_visit_id'] ?? $officeRunStop->office_visit_id,
            'outcome' => $validated['outcome'] ?? $officeRunStop->outcome,
            'notes' => $validated['notes'] ?? $officeRunStop->notes,
            'skip_reason' => $validated['skip_reason'] ?? $officeRunStop->skip_reason,
            'cancel_reason' => $validated['cancel_reason'] ?? $officeRunStop->cancel_reason,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Run stop updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfficeRouteRunStop $officeRunStop)
    {
        // CHANGED: Captured run id before delete so redirect still works.
        $officeRouteRunId = $officeRunStop->office_route_run_id;

        $officeRunStop->delete();

        return back()
            ->with('success', 'Run stop removed successfully.');
    }
}
