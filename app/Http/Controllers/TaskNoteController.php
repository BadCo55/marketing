<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskNote;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreTaskNoteRequest;
use App\Http\Requests\UpdateTaskNoteRequest;

class TaskNoteController extends Controller
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
    public function store(StoreTaskNoteRequest $request)
    {
        DB::beginTransaction();
        try {
            TaskNote::create($request->all());
            DB::commit();
            $tasks = Task::with([
                'assignedTo',
                'createdBy',
                'notes' => function ($query) {
                    $query->orderBy('created_at', 'desc'); // Order notes by newest first
                },
                'notes.createdBy',
            ])->get();
            return back()->with([
                'success' => 'The note was created successfully!',
                'task' => $tasks
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('There was an error creating a task note.', ['error' => $e->getMessage(), 'data' => $request->all()]);
            return back()->with('error', 'There was an error creating the note. Please try again.');
        }
        dd($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskNote $taskNote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskNote $taskNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskNoteRequest $request, TaskNote $taskNote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskNote $taskNote)
    {
        //
    }
}
