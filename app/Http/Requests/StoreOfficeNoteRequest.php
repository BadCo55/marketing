<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeNoteRequest extends FormRequest
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
            'priority' => 'required|string',
            'is_pinned' => 'required|boolean',
            'assigned_to' => 'required|integer',
            'requires_follow_up' => 'required|boolean',
            'note' => 'required|string|max:500',
            'office_id' => 'required|exists:offices,id'
        ];
    }
}
