<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSequenceRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_audience' => ['nullable', 'string', 'max:255'],

            'steps' => ['nullable', 'array'],
            'steps.*.id' => ['nullable', 'integer', 'exists:sequence_steps,id'],
            'steps.*.type' => [
                'required',
                'string',
                'in:in_person_visit,email,phone_call,text_message,task,snail_mail,social_media_interaction',
            ],
            'steps.*.interval' => ['nullable', 'integer', 'min:0'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.template' => ['nullable', 'string'],
            'steps.*.step_number' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (is_array($this->steps)) {
            $normalizedSteps = collect($this->steps)
                ->values()
                ->map(function ($step, $index) {
                    return [
                        'id' => $step['id'] ?? null,
                        'type' => $step['type'] ?? null,
                        'interval' => isset($step['interval']) && $step['interval'] !== ''
                            ? (int) $step['interval']
                            : 0,
                        'description' => $step['description'] ?? null,
                        'template' => $step['template'] ?? null,
                        'step_number' => $index + 1,
                    ];
                })
                ->toArray();

            $this->merge([
                'steps' => $normalizedSteps,
            ]);
        }
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a sequence name.',

            'steps.*.type.required' => 'Each step must have a type.',
            'steps.*.type.in' => 'One of the selected step types is invalid.',
            'steps.*.interval.integer' => 'Each step interval must be a whole number.',
            'steps.*.interval.min' => 'Each step interval must be 0 or greater.',
            'steps.*.step_number.integer' => 'Each step number must be a whole number.',
            'steps.*.step_number.min' => 'Each step number must be at least 1.',
            'steps.*.id.exists' => 'One of the sequence steps could not be found.',
        ];
    }
}
