<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Office>
 */
class OfficeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_company' => fake()->company(),
            'office_name' => null,
            'office_phone' => fake()->phoneNumber(),
            'street_address' => fake()->streetAddress(),
            'unit_number' => null,
            'city' => fake()->city(),
            'state' => 'FL',
            'county' => 'Broward County',
            'zip_code' => '33331',
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'place_id' => fake()->text(25),
            'image' => null,
        ];
    }
}
