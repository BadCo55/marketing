<?php

namespace App\Http\Controllers;

use App\Models\Sequence;
use App\Models\SequenceStep;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreSequenceRequest;
use App\Http\Requests\UpdateSequenceRequest;

class SequenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sequences = Sequence::with('steps')->get();
        foreach ($sequences as $sequence) {
            $sequence['step_count'] = count($sequence->steps);
        }
        return inertia(
            'Sequence/Index',
            [
                'sequences' => $sequences
            ],
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia(
            'Sequence/Create',
            [

            ],
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSequenceRequest $request)
    {
        $validated = $request->validated();
        $sequence = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'target_audience' => $validated['target_audience'],
        ];
        $steps = $validated['steps'];
        try {
            DB::beginTransaction();
            $sequence = Sequence::create($sequence);
            foreach ($steps as $index => $step) {
                $step['sequence_id'] = $sequence->id;
                $step['step_number'] = $index + 1;
                SequenceStep::create($step);
            };
            DB::commit();
            return redirect()
                ->route('sequence.show', $sequence)
                ->with('success', 'The sequence was created successfully.');
        } catch (\Exception $error) {
            DB::rollback();
            Log::error('There was an error creating a sequence.', [
                'error' => $error->getMessage(),
                'data' => $validated,
            ]);
            return back()->with('error', 'There was an error creating the sequence. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sequence $sequence)
    {
        $sequence->load('steps.notes');
        return inertia(
            'Sequence/Show',
            [
                'sequence' => $sequence
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sequence $sequence)
    {
        return inertia(
            'Sequence/Edit',
            [
                'sequence' => $sequence->load('steps')
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSequenceRequest $request, Sequence $sequence)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $sequence) {
            // CHANGED: Updated the main sequence record.
            $sequence->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'target_audience' => $validated['target_audience'] ?? null,
            ]);

            // CHANGED: Removed existing steps so the submitted order becomes the source of truth.
            $sequence->steps()->delete();

            // CHANGED: Rebuilt the steps in the exact order submitted from the form.
            foreach (($validated['steps'] ?? []) as $index => $step) {
                $sequence->steps()->create([
                    'type' => $step['type'],
                    'interval' => $step['interval'] ?? 0,
                    'description' => $step['description'] ?? null,
                    'template' => $step['template'] ?? null,
                    'step_number' => $step['step_number'] ?? ($index + 1),
                ]);
            }
        });

        return back()->with('success', 'Sequence updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sequence $sequence)
    {
        // CHANGED: Delete the sequence (steps will cascade delete automatically)
        $sequence->delete();

        return redirect()
            ->route('sequence.index')
            ->with('success', 'Sequence deleted successfully.');
    }
}
