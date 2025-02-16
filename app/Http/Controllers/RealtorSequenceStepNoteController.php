<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\RealtorSequenceStepNote;
use App\Http\Requests\StoreRealtorSequenceStepNoteRequest;
use App\Http\Requests\UpdateRealtorSequenceStepNoteRequest;

class RealtorSequenceStepNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRealtorSequenceStepNoteRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        $validated['created_by'] = $user->id;
        $validated['status_description'] = $validated['status']['description'];
        $validated['status'] = $validated['status']['label'];

        DB::beginTransaction();
        try {
            RealtorSequenceStepNote::create($validated);
            DB::commit();
            return back()->with('success', 'The note was created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating a sequence step note.', ['error' => $e->getMessage()]);
            return back()->with('error', 'There was an error creating the note. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RealtorSequenceStepNote $realtorSequenceStepNote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RealtorSequenceStepNote $realtorSequenceStepNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRealtorSequenceStepNoteRequest $request, RealtorSequenceStepNote $realtorSequenceStepNote)
    {
        $validated = $request->validated();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RealtorSequenceStepNote $realtorSequenceStepNote)
    {
        //
    }
}
