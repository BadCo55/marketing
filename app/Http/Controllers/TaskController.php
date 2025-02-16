<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskNote;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with('assignedTo', 'createdBy')->get();
        $users = User::all();
        return inertia(
            'Task/Index',
            [
                'tasks' => $tasks,
                'users' => $users,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia(
            'Task/Create',
            [

            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Task::create($validated);
            DB::commit();
            return back()->with([
                'success' => 'The task was created successfully!',
                'data' => Task::with(['assignedTo', 'createdBy'])->get(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Catch specific database-related errors
            DB::rollBack();
            logger()->error('Database error while creating a task.', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);
            return back()->with('error', 'There was a database error creating the task. Please try again.');
        } catch (\Exception $e) {
            // Catch general errors
            DB::rollBack();
            logger()->error('Error creating a task.', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);
            return back()->with('error', 'There was an error creating the task. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load([
            'createdBy',
            'notes' => function ($query) {
                $query->orderBy('created_at', 'desc'); // Order notes by newest first
            },
            'notes.createdBy',
        ]);
        return inertia(
            'Task/Show',
            [
                'task' => $task,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        return inertia(
            'Task/Edit',
            [

            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        if ($request->input('request_type') === 'status_update') {
            DB::beginTransaction();
            try {
                $task->update($request->all());
                DB::commit();
                return back()->with([
                    'success' => 'Status was updated successfully!',
                    'task' => $task,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                logger()->error('An error occurred when updating a task status', ['error' => $e->getMessage()]);
                return back()->with('error', 'There was an error updating the status.');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
