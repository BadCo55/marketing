<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeRouteRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'office_route_id' => ['required', 'integer', 'exists:office_routes,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['required', 'string', 'in:not_started,in_progress,completed,cancelled'],
            'run_date' => ['nullable', 'date'],
            'started_at' => ['nullable', 'date'],
            'start_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'start_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'office_route_id' => $this->office_route_id ?: $this->route('office_route')?->id,
        ]);
    }

    public function messages(): array
    {
        return [
            'office_route_id.required' => 'A route is required to start a run.',
            'office_route_id.exists' => 'The selected route could not be found.',
            'status.required' => 'Please select an initial status.',
            'status.in' => 'The selected run status is invalid.',
        ];
    }
}
