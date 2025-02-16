<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeRequest extends FormRequest
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
            'parent_company' => 'required|string|max:255',
            'office_name' => 'nullable|string|max:255',
            'office_phone' => 'nullable|string|max:255|unique:offices,office_phone',
            'street_address' => 'required|string|max:255',
            'unit_number' => 'nullable|string|max:50',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:50',
            'county' => 'required|string|max:100',
            'zip_code' => 'required|string|max:25',
            'latitude' => 'required|numeric|between:-90,90|unique:offices,latitude,unit_number',
            'longitude' => 'required|numeric|between:-180,180|unique:offices,longitude,unit_number',
            'place_id' => 'required|string|max:255|unique:offices,place_id,NULL,id,unit_number,' . $this->unit_number,
        ];
    }

    public function messages()
    {
        return [
            'street_address.required' => 'Street address is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'zip_code.required' => 'Zip code is required.',
            'latitude.required' => 'Latitude is required.',
            'longitude.required' => 'Longitude is required.',
            'place_id.required' => 'Place ID is required.',
            'place_id.unique' => 'An office with this address and unit number already exists.',
        ];
    }
}
