<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeRouteRunRequest;
use App\Http\Requests\UpdateOfficeRouteRunRequest;
use App\Models\OfficeRoute;
use App\Models\OfficeRouteRun;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficeRouteRunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // CHANGED: Added run history listing with useful eager loading.
        $runs = OfficeRouteRun::with([
            'officeRoute',
            'assignedTo',
            'startedBy',
            'completedBy',
            'createdBy',
            'orderedStops.office',
        ])
            ->orderByDesc('id')
            ->get();

        return inertia('OfficeRouteRun/Index', [
            'office_route_runs' => $runs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // CHANGED: Route runs are normally started from a route show page or action button.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfficeRouteRunRequest $request)
    {
        // CHANGED: Creates a new execution run from a reusable route and snapshots its stops.
        $validated = $request->validated();

        $officeRouteRun = DB::transaction(function () use ($validated) {
            /** @var OfficeRoute $officeRoute */
            $officeRoute = OfficeRoute::with(['orderedStops'])->findOrFail($validated['office_route_id']);

            $plannedStops = $officeRoute->orderedStops;

            // CHANGED: Created the run record.
            $run = OfficeRouteRun::create([
                'office_route_id' => $officeRoute->id,
                'assigned_to' => $validated['assigned_to'] ?? $officeRoute->assigned_to,
                'started_by' => $validated['started_by'] ?? null,
                'completed_by' => null,
                'status' => $validated['status'] ?? 'not_started',
                'run_date' => $validated['run_date'] ?? $officeRoute->route_date,
                'current_stop_order' => $plannedStops->count() ? 1 : null,
                'planned_stop_count' => $plannedStops->count(),
                'completed_stop_count' => 0,
                'skipped_stop_count' => 0,
                'cancelled_stop_count' => 0,
                'started_at' => $validated['started_at'] ?? null,
                'completed_at' => null,
                'cancelled_at' => null,
                'start_latitude' => $validated['start_latitude'] ?? null,
                'start_longitude' => $validated['start_longitude'] ?? null,
                'end_latitude' => null,
                'end_longitude' => null,
                'actual_total_drive_minutes' => null,
                'actual_total_duration_minutes' => null,
                'actual_total_drive_miles' => null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // CHANGED: Snapshotted each planned stop into run stops.
            foreach ($plannedStops as $index => $stop) {
                $run->stops()->create([
                    'office_route_stop_id' => $stop->id,
                    'office_id' => $stop->office_id,
                    'planned_stop_order' => $stop->stop_order ?? ($index + 1),
                    'actual_stop_order' => $stop->stop_order ?? ($index + 1),
                    'status' => 'pending',
                    'parent_company' => $stop->parent_company,
                    'office_name' => $stop->office_name,
                    'street_address' => $stop->street_address,
                    'unit_number' => $stop->unit_number,
                    'city' => $stop->city,
                    'state' => $stop->state,
                    'zip_code' => $stop->zip_code,
                    'latitude' => $stop->latitude,
                    'longitude' => $stop->longitude,
                    'office_phone' => $stop->office_phone,
                    'priority' => $stop->priority ?? 3,
                    'required' => $stop->required ?? false,
                    'estimated_drive_minutes_from_previous' => $stop->estimated_drive_minutes_from_previous,
                    'estimated_drive_miles_from_previous' => $stop->estimated_drive_miles_from_previous,
                    'notes' => $stop->notes,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            return $run->load([
                'officeRoute',
                'assignedTo',
                'startedBy',
                'completedBy',
                'createdBy',
                'orderedStops.office',
            ]);
        });

        return redirect()
            ->route('office-run.active', $officeRouteRun)
            ->with('success', 'Route run created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OfficeRouteRun $officeRun)
    {
        // CHANGED: Loaded relationships for the run show / active execution screen.
        $officeRun->load([
            'officeRoute',
            'assignedTo',
            'startedBy',
            'completedBy',
            'createdBy',
            'updatedBy',
            'orderedStops.office',
            'orderedStops.officeVisit',
        ]);


        return inertia('OfficeRouteRun/Show', [
            'office_route_run' => $officeRun,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfficeRouteRun $officeRun)
    {
        // CHANGED: Loaded relationships for run editing if needed later.
        $officeRun->load([
            'officeRoute',
            'orderedStops.office',
        ]);

        return inertia('OfficeRouteRun/Edit', [
            'office_route_run' => $officeRun,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfficeRouteRunRequest $request, OfficeRouteRun $officeRun)
    {
        // CHANGED: Added run-level update support for status/progress changes.
        $validated = $request->validated();

        $officeRun->update([
            'assigned_to' => $validated['assigned_to'] ?? $officeRun->assigned_to,
            'started_by' => $validated['started_by'] ?? $officeRun->started_by,
            'completed_by' => $validated['completed_by'] ?? $officeRun->completed_by,
            'status' => $validated['status'] ?? $officeRun->status,
            'run_date' => $validated['run_date'] ?? $officeRun->run_date,
            'current_stop_order' => $validated['current_stop_order'] ?? $officeRun->current_stop_order,
            'planned_stop_count' => $validated['planned_stop_count'] ?? $officeRun->planned_stop_count,
            'completed_stop_count' => $validated['completed_stop_count'] ?? $officeRun->completed_stop_count,
            'skipped_stop_count' => $validated['skipped_stop_count'] ?? $officeRun->skipped_stop_count,
            'cancelled_stop_count' => $validated['cancelled_stop_count'] ?? $officeRun->cancelled_stop_count,
            'started_at' => $validated['started_at'] ?? $officeRun->started_at,
            'completed_at' => $validated['completed_at'] ?? $officeRun->completed_at,
            'cancelled_at' => $validated['cancelled_at'] ?? $officeRun->cancelled_at,
            'start_latitude' => $validated['start_latitude'] ?? $officeRun->start_latitude,
            'start_longitude' => $validated['start_longitude'] ?? $officeRun->start_longitude,
            'end_latitude' => $validated['end_latitude'] ?? $officeRun->end_latitude,
            'end_longitude' => $validated['end_longitude'] ?? $officeRun->end_longitude,
            'actual_total_drive_minutes' => $validated['actual_total_drive_minutes'] ?? $officeRun->actual_total_drive_minutes,
            'actual_total_duration_minutes' => $validated['actual_total_duration_minutes'] ?? $officeRun->actual_total_duration_minutes,
            'actual_total_drive_miles' => $validated['actual_total_drive_miles'] ?? $officeRun->actual_total_drive_miles,
            'notes' => $validated['notes'] ?? $officeRun->notes,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('office-run.show', $officeRun)
            ->with('success', 'Route run updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfficeRouteRun $officeRun)
    {
        // CHANGED: Soft-deleted the run.
        $officeRun->delete();

        return redirect()
            ->route('office-run.index')
            ->with('success', 'Route run deleted successfully.');
    }

    public function start(OfficeRoute $officeRoute)
    {
        $officeRoute->load([
            'stops.office',
            'assignedTo',
        ]);

        return inertia('OfficeRouteRun/Start', [
            'office_route' => $officeRoute,
            'users' => User::all(),
        ]);
    }

    public function active(OfficeRouteRun $officeRouteRun)
    {
        // CHANGED: Eager load everything the Active.vue page needs
        $officeRouteRun->load([
            'officeRoute',
            'assignedTo',
            'stops.office', // VERY important for UI
        ]);

        // CHANGED: Optional - compute counts server-side (nice for performance + consistency)
        $officeRouteRun->completed_stop_count = $officeRouteRun->stops
            ->where('status', 'completed')
            ->count();

        $officeRouteRun->skipped_stop_count = $officeRouteRun->stops
            ->where('status', 'skipped')
            ->count();

        $officeRouteRun->planned_stop_count = $officeRouteRun->stops->count();

        // CHANGED: Optional - determine current stop server-side (more reliable than frontend)
        $officeRouteRun->current_stop = $officeRouteRun->stops
            ->sortBy('actual_stop_order')
            ->first(function ($stop) {
                return in_array($stop->status, ['pending', 'in_progress', 'arrived']);
            });

        return inertia('OfficeRouteRun/Active', [
            'office_route_run' => $officeRouteRun,
        ]);
    }
}
