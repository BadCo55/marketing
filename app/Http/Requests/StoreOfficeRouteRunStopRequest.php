<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeRouteRunStopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // CHANGED: Allow authenticated users to create run stops.
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
            // CHANGED: Added core run-stop relationships.
            'office_route_run_id' => ['required', 'integer', 'exists:office_route_runs,id'],
            'office_route_stop_id' => ['nullable', 'integer', 'exists:office_route_stops,id'],
            'office_id' => ['nullable', 'integer', 'exists:offices,id'],
            'office_visit_id' => ['nullable', 'integer', 'exists:office_visits,id'],

            // CHANGED: Added execution order and status validation.
            'planned_stop_order' => ['nullable', 'integer', 'min:1'],
            'actual_stop_order' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'in:pending,in_progress,arrived,completed,skipped,cancelled'],

            // CHANGED: Added snapshot validation.
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

            // CHANGED: Added planning/execution detail validation.
            'priority' => ['nullable', 'integer', 'between:1,5'],
            'required' => ['nullable', 'boolean'],
            'estimated_drive_minutes_from_previous' => ['nullable', 'integer', 'min:0'],
            'estimated_drive_miles_from_previous' => ['nullable', 'numeric', 'min:0'],
            'actual_drive_minutes_from_previous' => ['nullable', 'integer', 'min:0'],
            'actual_drive_miles_from_previous' => ['nullable', 'numeric', 'min:0'],

            // CHANGED: Added execution timestamps.
            'started_at' => ['nullable', 'date'],
            'arrived_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'skipped_at' => ['nullable', 'date'],
            'cancelled_at' => ['nullable', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],

            // CHANGED: Added outcome and notes validation.
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
            'required' => filter_var($this->required ?? false, FILTER_VALIDATE_BOOLEAN),
            'priority' => isset($this->priority) ? (int) $this->priority : $this->priority,
            'planned_stop_order' => isset($this->planned_stop_order) ? (int) $this->planned_stop_order : $this->planned_stop_order,
            'actual_stop_order' => isset($this->actual_stop_order) ? (int) $this->actual_stop_order : $this->actual_stop_order,
        ]);
    }
}
