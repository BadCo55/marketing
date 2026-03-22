<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfficeRouteRunStopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // CHANGED: Allow authenticated users to update run stops.
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
            'office_route_stop_id' => ['nullable', 'integer', 'exists:office_route_stops,id'],
            'office_id' => ['nullable', 'integer', 'exists:offices,id'],
            'office_visit_id' => ['nullable', 'integer', 'exists:office_visits,id'],

            'planned_stop_order' => ['nullable', 'integer', 'min:1'],
            'actual_stop_order' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'in:pending,in_progress,arrived,completed,skipped,cancelled'],

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

            'priority' => ['nullable', 'integer', 'between:1,5'],
            'required' => ['nullable', 'boolean'],
            'estimated_drive_minutes_from_previous' => ['nullable', 'integer', 'min:0'],
            'estimated_drive_miles_from_previous' => ['nullable', 'numeric', 'min:0'],
            'actual_drive_minutes_from_previous' => ['nullable', 'integer', 'min:0'],
            'actual_drive_miles_from_previous' => ['nullable', 'numeric', 'min:0'],

            'started_at' => ['nullable', 'date'],
            'arrived_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'skipped_at' => ['nullable', 'date'],
            'cancelled_at' => ['nullable', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],

            'outcome' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'skip_reason' => ['nullable', 'string'],
            'cancel_reason' => ['nullable', 'string'],
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
            'planned_stop_order' => isset($this->planned_stop_order) ? (int) $this->planned_stop_order : $this->planned_stop_order,
            'actual_stop_order' => isset($this->actual_stop_order) ? (int) $this->actual_stop_order : $this->actual_stop_order,
        ]);
    }
}
