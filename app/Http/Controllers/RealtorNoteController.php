<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Realtor;
use App\Models\RealtorNote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreRealtorNoteRequest;
use App\Http\Requests\UpdateRealtorNoteRequest;

class RealtorNoteController extends Controller
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
    public function store(StoreRealtorNoteRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();
        if ($validated['assigned_to'] === 0) {
            $validated['assigned_to'] = null;
        }
        $validated['created_by'] = $user->id;
        DB::beginTransaction();
        try {
            $note = RealtorNote::create($validated);
            $note->load(['createdBy', 'assignedTo']); // Load relationships
            // Create task if needed
            if ($note->requires_follow_up === true) {
                $realtor = Realtor::find($note->realtor_id);
                $task = new Task([
                    'title' => 'Follow Up with ' . $realtor->formatted_full_name,
                    'description' => $note->note,
                    'assigned_to' => $note->assigned_to,
                    'due_date' => null,
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                ]);
                $note->tasks()->save($task);
            }
            $note->append('formatted_created_at'); // Append the formatted date
            DB::commit();
            return back()->with([
                'success' => 'The realtor note was created successfully!',
                'realtor' => Realtor::with([
                        'notes' => function ($query) {
                            $query->orderBy('created_at', 'desc'); // Sort newest first
                        },
                        'notes.createdBy', 
                        'notes.assignedTo'
                    ])
                    ->findOrFail($note->realtor_id)
                    ->append('avatar_url'),
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
    public function show(RealtorNote $realtorNote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RealtorNote $realtorNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRealtorNoteRequest $request, RealtorNote $realtorNote)
    {
        // Check the request type
        if ($request->request_type === 'remove_pin') {
            // Update the `is_pinned` field
            $realtorNote->update([
                'is_pinned' => $request->input('is_pinned'),
            ]);
            return back()->with('success', 'The note was unpinned successfully!');
        } else if ($request->request_type === 'add_pin') {
            $realtorNote->update([
                'is_pinned' => true,
            ]);
            return back()->with('success', 'The note was pinned successfully!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RealtorNote $realtorNote)
    {
        try {
            $realtorNote->delete();
            return back()->with('success', 'The note was deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting realtor note.', ['error' => $e->getMessage()]);
            session()->flash('error', 'There was an error deleting the note. Please try again.');
        }
    }
}
