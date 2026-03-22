<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSequenceStepRequest extends FormRequest
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
            'sequence_id' => ['sometimes', 'nullable', 'integer', 'exists:sequences,id'],
            'type' => [
                'required',
                'string',
                'in:in_person_visit,email,phone_call,text_message,task,snail_mail,social_media_interaction',
            ],
            'interval' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'template' => ['nullable', 'string'],
            'step_number' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'interval' => $this->interval === '' || $this->interval === null
                ? 0
                : (int) $this->interval,
        ]);
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Please select a step type.',
            'type.in' => 'The selected step type is invalid.',
            'interval.integer' => 'The interval must be a whole number.',
            'interval.min' => 'The interval must be 0 or greater.',
            'step_number.integer' => 'The step number must be a whole number.',
            'step_number.min' => 'The step number must be at least 1.',
            'sequence_id.exists' => 'The selected sequence could not be found.',
        ];
    }
}
