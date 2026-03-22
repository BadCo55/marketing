<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfficeRouteStopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // CHANGED: Allow authenticated users to update planning stops.
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
            'office_id' => ['nullable', 'integer', 'exists:offices,id'],
            'stop_order' => ['nullable', 'integer', 'min:1'],
            'priority' => ['nullable', 'integer', 'between:1,5'],
            'required' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],

            'parent_company' => ['nullable', 'string', 'max:255'],
            'office_name' => ['nullable', 'string', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'unit_number' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:10'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'office_phone' => ['nullable', 'string', 'max:255'],

            'estimated_drive_minutes_from_previous' => ['nullable', 'integer', 'min:0'],
            'estimated_drive_miles_from_previous' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'required' => $this->has('required')
                ? filter_var($this->required, FILTER_VALIDATE_BOOLEAN)
                : $this->required,
            'priority' => isset($this->priority) ? (int) $this->priority : $this->priority,
            'stop_order' => isset($this->stop_order) ? (int) $this->stop_order : $this->stop_order,
        ]);
    }
}
