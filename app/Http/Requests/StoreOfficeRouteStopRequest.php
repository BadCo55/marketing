<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeRouteStopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // CHANGED: Allow authenticated users to create planning stops.
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
            // CHANGED: Added required planning stop relationships.
            'office_route_id' => ['required', 'integer', 'exists:office_routes,id'],
            'office_id' => ['nullable', 'integer', 'exists:offices,id'],
            'stop_order' => ['required', 'integer', 'min:1'],

            // CHANGED: Added planning metadata validation.
            'priority' => ['nullable', 'integer', 'between:1,5'],
            'required' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],

            // CHANGED: Added snapshot field validation.
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

            // CHANGED: Added estimate validation.
            'estimated_drive_minutes_from_previous' => ['nullable', 'integer', 'min:0'],
            'estimated_drive_miles_from_previous' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // CHANGED: Normalized boolean and integer-like values.
        $this->merge([
            'required' => filter_var($this->required ?? false, FILTER_VALIDATE_BOOLEAN),
            'priority' => isset($this->priority) ? (int) $this->priority : $this->priority,
            'stop_order' => isset($this->stop_order) ? (int) $this->stop_order : $this->stop_order,
        ]);
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'office_route_id.required' => 'A route is required.',
            'office_route_id.exists' => 'The selected route could not be found.',
            'office_id.exists' => 'The selected office could not be found.',
            'stop_order.required' => 'A stop order is required.',
            'stop_order.min' => 'Stop order must be at least 1.',
            'priority.between' => 'Priority must be between 1 and 5.',
        ];
    }
}
