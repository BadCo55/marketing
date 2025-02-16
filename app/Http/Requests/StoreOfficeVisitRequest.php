<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeVisitRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            // 'office' => 'required|array',
            'office_id' => 'required|exists:offices,id',
            'visit_date' => 'required|date',
            'duration' => 'required|integer|max:50',
            'rating' => 'required|integer',
            'purpose' => 'required|string|max:50',
            'initial_impressions' => 'required|string|max:500',
            'interest_level' => 'required|integer',
            'follow_up_required' => 'required|boolean',
            'materials_presented' => 'nullable|array',
            'discussion_topics' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500',
            'next_action' => 'nullable|string|max:500|required_if:follow_up_required,true',
            'next_action_date' => 'nullable|date|required_if:follow_up_required,true',
        ];
    }

    protected function prepareForValidation()
    {

        $this->merge([
            'user_id' => Auth::id(),
        ]);
        
        if ($this->has('visit_date') && $this->input('visit_date')) {
            try {
                // Reformat the date to Y-m-d format
                $this->merge([
                    'visit_date' => Carbon::createFromFormat('m/d/Y', $this->input('visit_date'))->format('Y-m-d'),
                ]);
            } catch (\Exception $e) {
                // Optionally, log or handle invalid date formats
                logger()->error('Invalid date format for visit_date', ['error' => $e->getMessage()]);
            }
        }

        if ($this->has('next_action_date') && $this->input('next_action_date')) {
            try {
                $this->merge([
                    'next_action_date' => Carbon::createFromFormat('m/d/Y', $this->input('next_action_date'))->format('Y-m-d'),
                ]);
            } catch (\Exception $e) {
                logger()->error('Invalid date format for next_action_date', ['error' => $e->getMessage()]);
            }
        }

        // Extract 'office_id' from the 'office' object if present
        if ($this->has('office') && is_array($this->input('office'))) {
            try {
                $office = $this->input('office');
                if (isset($office['id'])) {
                    $this->merge([
                        'office_id' => $office['id'],
                    ]);
                } else {
                    logger()->warning('Office array does not contain an ID', ['office' => $office]);
                }
            } catch (\Exception $e) {
                logger()->error('Unable to set office_id', ['error' => $e->getMessage()]);
            }
        }

        $dateFields = ['visit_date', 'next_action_date'];
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
    }
}
