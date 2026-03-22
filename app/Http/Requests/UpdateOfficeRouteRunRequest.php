<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfficeRouteRunRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // CHANGED: Allow authenticated users to update route runs.
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
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'started_by' => ['nullable', 'integer', 'exists:users,id'],
            'completed_by' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'string', 'in:not_started,in_progress,paused,completed,cancelled'],
            'run_date' => ['nullable', 'date'],
            'current_stop_order' => ['nullable', 'integer', 'min:1'],
            'planned_stop_count' => ['nullable', 'integer', 'min:0'],
            'completed_stop_count' => ['nullable', 'integer', 'min:0'],
            'skipped_stop_count' => ['nullable', 'integer', 'min:0'],
            'cancelled_stop_count' => ['nullable', 'integer', 'min:0'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'cancelled_at' => ['nullable', 'date'],
            'start_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'start_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'end_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'end_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'actual_total_drive_minutes' => ['nullable', 'integer', 'min:0'],
            'actual_total_duration_minutes' => ['nullable', 'integer', 'min:0'],
            'actual_total_drive_miles' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
