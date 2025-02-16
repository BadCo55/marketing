<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Office;
use App\Models\OfficeVisit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreOfficeVisitRequest;
use App\Http\Requests\UpdateOfficeVisitRequest;

class OfficeVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visits = OfficeVisit::query()
            ->orderBy('visit_date', 'desc')
            ->with(['createdBy', 'office']) // Preload the related `createdBy` data
            ->get();
        $visits->append([
            'formatted_materials_presented',
            'formatted_visit_date',
            'formatted_rating',
            'formatted_interest_level',
            'formatted_duration',
            'formatted_purpose',
            'formatted_next_action_date',
            'formatted_follow_up_required',
        ]);
        $users = User::all();
        return inertia(
            'Visit/Index',
            [
                'visits' => $visits,
                'users' => $users,
            ],
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allOffices = Office::all();
        foreach ($allOffices as $office) {
            $office->append('image_url');
        }
        return inertia(
            'Visit/Create',
            [
                'offices' => $allOffices,
            ],
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfficeVisitRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $visit = OfficeVisit::create($validated);
            if (!empty($visit->follow_up_required) && $visit->follow_up_required === true) {
                $task = new Task([
                    'title' => 'Next actions for ' . $visit->purpose . ' visit on ' . $visit->formatted_visit_date,
                    'description' => $visit->next_action,
                    'assigned_to' => Auth::id(),
                    'due_date' => $visit->next_action_date ? $visit->next_action_date : null,
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                ]);
                $visit->tasks()->save($task);
            }
            DB::commit();
            return redirect()->route('office-visit.show', $visit->id)->with('success', 'The visit was submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An error occurred while attempting to store an office visit.', ['error' => $e->getMessage()]);
            return back()->with('error', 'An error occurred while attempting to submit the visit. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OfficeVisit $officeVisit)
    {
        $officeVisit->load([
            'createdBy',
            'office'
        ]);
        if ($officeVisit->office) {
            $officeVisit->office->append('image_url');
        }
        $officeVisit->append([
            'formatted_materials_presented',
            'formatted_visit_date',
            'formatted_rating',
            'formatted_interest_level',
            'formatted_duration',
            'formatted_purpose',
            'formatted_next_action_date',
            'formatted_follow_up_required',
        ]);
        return inertia(
            'Visit/Show',
            [
                'visit' => $officeVisit,
            ],
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfficeVisit $officeVisit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfficeVisitRequest $request, OfficeVisit $officeVisit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfficeVisit $officeVisit)
    {
        //
    }
}
