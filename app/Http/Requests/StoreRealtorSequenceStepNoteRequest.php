<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRealtorSequenceStepNoteRequest extends FormRequest
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
            'sequence_id' => 'required|exists:sequences,id',
            'step_id' => 'required|exists:sequence_steps,id',
            'status' => 'required',
            'next_update' => 'required|string',
            'note' => 'required|string|max:500',
        ];
    }
}
