<?php

namespace App\Http\Controllers;

use App\Models\Realtor;
use App\Models\Sequence;
use Illuminate\Http\Request;
use App\Models\RealtorSequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreRealtorSequenceRequest;
use App\Http\Requests\UpdateRealtorSequenceRequest;

class RealtorSequenceController extends Controller
{
    /**
     * Assign a realtor to a sequence
     */
    public function store(StoreRealtorSequenceRequest $request)
    {
        DB::beginTransaction();
        try {
            RealtorSequence::create($request->validated());
            DB::commit();
            $realtorSequences = RealtorSequence::where('realtor_id', $request->input('realtor_id'))
                ->with(['sequence', 'realtor']) // Eager load relationships if needed
                ->get();
            $realtor = Realtor::with('activeSequences')->get();
            return back()->with([
                'success' => 'The sequence was added successfully!',
                'realtor' => $realtor,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('There was an error adding the realtor to a sequence.', ['error' => $e->getMessage()]);
            return back()->with('error', 'There was an error adding the sequence. Please try again.');
        }
    }

    /**
     * Update the sequence
     */
    public function update(UpdateRealtorSequenceRequest $request, RealtorSequence $realtor_sequence)
    {
        $validated = $request->validated();
    
        if ($validated['request_type'] === 'increment_step') {
            try {
                DB::beginTransaction();
    
                if ($realtor_sequence->status === 'not_started') {
                    $realtor_sequence->status = 'active';
                }
    
                // Check if the current step can be incremented
                if ($realtor_sequence->current_step < $realtor_sequence->total_steps) {
                    $realtor_sequence->current_step++;
                    $realtor_sequence->save();
                    DB::commit();
                    $realtor = Realtor::with('activeSequences')->where('id', $realtor_sequence->realtor_id)->get();
                    return back()->with([
                        'success' => 'The sequence step was increased successfully!',
                        'realtor' => $realtor,
                    ]);
                } 
                
                $realtor_sequence->status = 'completed';
                $realtor_sequence->save();
                $realtor = Realtor::with('activeSequences')->where('id', $realtor_sequence->realtor_id)->get();
                DB::commit();
                return back()->with([
                    'success' => 'The sequence was completed successfully!',
                    'realtor' => $realtor 
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                logger()->error('There was an error incrementing the sequence step.', ['error' => $e->getMessage()]);
                return back()->with('error', 'There was an error incrementing the sequence step.');
            }
        }
        return back()->with('error', 'Invalid request type.');
    }

    /**
     * Increment the sequence step
     */
    public function nextStep(Realtor $realtor, Sequence $sequence)
    {

        // Fetch the current step from the pivot table
        $currentStepNumber = $realtor->sequences()
            ->where('sequence_id', $sequence->id)
            ->first()
            ->pivot->current_step;

        $sequence = $sequence->load('steps');

        $currentStep = $sequence->steps->firstWhere('order', $currentStepNumber);

        $nextStepNumber = $currentStepNumber + 1;
        $nextStep = $sequence->steps->firstWhere('order', $nextStepNumber);

        // Check if the next step exists
        $sequenceStepsCount = $sequence->steps()->count();
        if ($nextStepNumber > $sequenceStepsCount) {
            return response()->json([
                'error' => 'Realtor has completed all steps in the sequence.',
            ], 400);
        }

        // Update current step in sequence
        $realtor->sequences()->updateExistingPivot($sequence->id, [
            'current_step' => $nextStepNumber,
        ]);

        // Fetch the updated sequence with all steps
        $updatedSequence = $realtor->sequences()
            ->where('sequence_id', $sequence->id)
            ->with('steps')
            ->first();

        // Add the dynamic current step and its metadata
        $currentStepNumber = $updatedSequence->steps->firstWhere('order', $updatedSequence->pivot->current_step);
        $stepsLength = $updatedSequence->steps->count();

        $progress = $currentStepNumber->order . '/' . $stepsLength;
        $updatedSequence->progress = $progress;

        $createdAt = $updatedSequence->pivot->created_at;
        $interval = $currentStepNumber->interval ?? 0;

        $dueBy = $interval > 0
            ? date('m/d/y g:i a', strtotime("+$interval days", strtotime($createdAt)))
            : date('m/d/y g:i a', strtotime($createdAt));

        $updatedSequence->added_on = date('m/d/y, g:i a', strtotime($currentStep->created_at));
        $updatedSequence->due_by = $dueBy;
        $updatedSequence->current_step = $currentStepNumber;

        // Log::debug('Updated Sequence:', $updatedSequence->toArray());

        // Return the updated sequence
        return response()->json($updatedSequence);
    }

    /**
     * Mark the sequence as complete
     */
    public function completeSequence(Realtor $realtor, Sequence $sequence) 
    {
        $realtor->sequences()->updateExistingPivot($sequence->id, ['current_step' => $sequence->steps()->count()]);
        return back()->with('success', 'Realtor has completed the sequence');
    }
}
