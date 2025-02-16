<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use App\Models\Sequence;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRealtorSequenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'realtor_id' => 'required|exists:realtors,id',
            'sequence_id' => [
                'required',
                'exists:sequences,id',
                Rule::unique('realtor_sequences')->where(function ($query) {
                    return $query->where('realtor_id', $this->realtor_id);
                }),
            ],
            'current_step' => 'required|integer',
            'start_date' => 'required|date',
            'next_step_due' => 'required|date',
            'status' => 'required|in:not_started,active,paused,cancelled'
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'current_step' => 0,
            'status' => 'not_started',
        ]);

        $dateFields = ['start_date', 'next_step_due'];
        foreach ($dateFields as $field) {
            if ($this->has($field) && $this->input($field)) {
                try {
                    // Parse and format the date as Y-m-d for the database
                    $this->merge([
                        $field => Carbon::parse($this->input($field))->format('Y-m-d'),
                    ]);
                } catch (\Exception $e) {
                    logger()->error("Invalid date format for {$field}", ['error' => $e->getMessage()]);
                }
            }
        }

        // Fetch the sequence and its first step (step_number = 1)
        $sequence = Sequence::findOrFail($this->input('sequence_id'));
        $firstStep = $sequence->steps->where('step_number', 1)->first();

        // Calculate the next_step_due date based on the interval of the first step
        if ($firstStep) {
            $startDate = Carbon::parse($this->input('start_date', Carbon::now()));
            $nextStepDue = $startDate->addDays($firstStep->interval);

            // Merge the calculated next_step_due into the request
            $this->merge([
                'next_step_due' => $nextStepDue->format('Y-m-d'),
            ]);
        } else {
            logger()->error('No step with step_number = 1 found for the sequence.', ['sequence_id' => $this->input('sequence_id')]);
            $this->merge([
                'next_step_due' => null,
            ]);
        }


    }
}
