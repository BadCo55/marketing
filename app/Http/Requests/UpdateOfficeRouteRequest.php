<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfficeRouteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // CHANGED: Allow authenticated users to update routes.
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
            // CHANGED: Added validation for reusable route fields.
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:draft,active,archived'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'route_date' => ['nullable', 'date'],
            'source_type' => ['nullable', 'string', 'in:manual,radius,csv,xlsx,mixed,excel'],
            'import_file_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],

            // CHANGED: Added validation for map/radius planning fields.
            'center_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'center_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'radius_miles' => ['nullable', 'numeric', 'min:0'],

            // CHANGED: Added optional route estimate fields.
            'estimated_total_drive_minutes' => ['nullable', 'integer', 'min:0'],
            'estimated_total_drive_miles' => ['nullable', 'numeric', 'min:0'],

            // CHANGED: Added validation for stops array.
            'stops' => ['required', 'array', 'min:1'],

            // CHANGED: Added per-stop validation for planning-only route stops.
            'stops.*.office_id' => ['required', 'integer', 'distinct', 'exists:offices,id'],
            'stops.*.stop_order' => ['nullable', 'integer', 'min:1'],
            'stops.*.priority' => ['nullable', 'integer', 'between:1,5'],
            'stops.*.required' => ['nullable', 'boolean'],
            'stops.*.notes' => ['nullable', 'string'],

            // CHANGED: Added snapshot validation for stop office data.
            'stops.*.parent_company' => ['nullable', 'string', 'max:255'],
            'stops.*.office_name' => ['nullable', 'string', 'max:255'],
            'stops.*.street_address' => ['nullable', 'string', 'max:255'],
            'stops.*.unit_number' => ['nullable', 'string', 'max:255'],
            'stops.*.city' => ['nullable', 'string', 'max:255'],
            'stops.*.state' => ['nullable', 'string', 'max:10'],
            'stops.*.zip_code' => ['nullable', 'string', 'max:20'],
            'stops.*.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'stops.*.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'stops.*.office_phone' => ['nullable', 'string', 'max:255'],

            // CHANGED: Added estimate validation for each stop.
            'stops.*.estimated_drive_minutes_from_previous' => ['nullable', 'integer', 'min:0'],
            'stops.*.estimated_drive_miles_from_previous' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // CHANGED: Normalized stops to always have clean sequential order and boolean required values.
        if (is_array($this->stops)) {
            $normalizedStops = collect($this->stops)
                ->values()
                ->map(function ($stop, $index) {
                    $stop['stop_order'] = $index + 1;
                    $stop['required'] = filter_var($stop['required'] ?? false, FILTER_VALIDATE_BOOLEAN);
                    $stop['priority'] = isset($stop['priority']) ? (int) $stop['priority'] : 3;

                    return $stop;
                })
                ->toArray();

            $this->merge([
                'stops' => $normalizedStops,
            ]);
        }
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a route name.',
            'status.in' => 'The selected route status is invalid.',
            'assigned_to.exists' => 'The selected assigned user could not be found.',
            'source_type.in' => 'The selected source type is invalid.',
            'stops.required' => 'Please add at least one office stop to the route.',
            'stops.min' => 'Please add at least one office stop to the route.',
            'stops.*.office_id.required' => 'Each stop must have an office selected.',
            'stops.*.office_id.exists' => 'One of the selected offices could not be found.',
            'stops.*.office_id.distinct' => 'The same office cannot be added to the route more than once.',
            'stops.*.priority.between' => 'Stop priority must be between 1 and 5.',
        ];
    }
}
