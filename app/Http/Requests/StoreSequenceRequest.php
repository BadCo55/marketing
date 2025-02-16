<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSequenceRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:sequences,name',
            'description' => 'nullable|string',
            'target_audience' => 'required|string|max:50',
            'steps' => 'required|array',
            'steps.*.type' => 'required|string|max:100', // Validates each step's type
            'steps.*.interval' => 'required|integer|min:0', // Validates interval as a positive integer
            'steps.*.description' => 'required|string', // Validates each step's description
            'steps.*.template' => 'nullable|string',
        ];
    }
}
