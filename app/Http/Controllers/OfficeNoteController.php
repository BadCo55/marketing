<?php

namespace App\Http\Controllers;

use App\Models\OfficeNote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreOfficeNoteRequest;
use App\Http\Requests\UpdateOfficeNoteRequest;
use App\Models\Office;

class OfficeNoteController extends Controller
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
    public function store(StoreOfficeNoteRequest $request)
    {

        $user = Auth::user();
        $validated = $request->validated();
        if ($validated['assigned_to'] === 0) {
            $validated['assigned_to'] = null;
        }
        $validated['created_by'] = $user->id;
        DB::beginTransaction();
        try {
            $note = OfficeNote::create($validated);
            $note->load(['createdBy', 'assignedTo']); // Load relationships
            $note->append('formatted_created_at'); // Append the formatted date
            DB::commit();
            return back()->with([
                'success' => 'The realtor note was created successfully!',
                'realtor' => Office::with(['notes.createdBy', 'notes.assignedTo'])
                    ->findOrFail($note->office_id)
                    ->append('image_url'),
            ]);;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating a realtor note.', ['error' => $e->getMessage()]);
            return back()->with('error', 'There was an error creating the realtor\'s note.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OfficeNote $officeNote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfficeNote $officeNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfficeNoteRequest $request, OfficeNote $officeNote)
    {
        // Check the request type
        if ($request->request_type === 'remove_pin') {
            // Update the `is_pinned` field
            $officeNote->update([
                'is_pinned' => $request->input('is_pinned'),
            ]);
            return back()->with('success', 'The note was unpinned successfully!');
        } else if ($request->request_type === 'add_pin') {
            $officeNote->update([
                'is_pinned' => true,
            ]);
            return back()->with('success', 'The note was pinned successfully!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfficeNote $officeNote)
    {
        try {
            $officeNote->delete();
            return back()->with('success', 'The note was deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting realtor note.', ['error' => $e->getMessage()]);
            session()->flash('error', 'There was an error deleting the note. Please try again.');
        }
    }
}
