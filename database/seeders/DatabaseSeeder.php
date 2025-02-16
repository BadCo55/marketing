<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Office;
use App\Models\Realtor;
use App\Models\Sequence;
use App\Models\SequenceStep;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone_number' => '954-654-5594',
            'email' => 'test@example.com',
        ]);

        // Sequence::factory()->create([
        //     'name' => 'New Realtor from Inspection',
        //     'description' => 'Used for a realtor met at an inspection we performed.',
        //     'target_audience' => 'Realtor',
        // ]);

        // SequenceStep::factory()->create([
        //     'sequence_id' => 1,
        //     'type' => 'Email',
        //     'interval' => 1,
        //     'description' => 'An initial email sent to introduce our services',
        //     'step_number' => 1,
        // ]);
        // SequenceStep::factory()->create([
        //     'sequence_id' => 1,
        //     'type' => 'Phone Call',
        //     'interval' => 3,
        //     'description' => 'A phone call to ensure the realtor got our email.',
        //     'step_number' => 2,
        // ]);
        // SequenceStep::factory()->create([
        //     'sequence_id' => 1,
        //     'type' => 'Social Media Interaction',
        //     'interval' => 5,
        //     'description' => 'A follow on social media platforms to re-emphasize our brand.',
        //     'step_number' => 3,
        // ]);

        // Office::factory(5)->create();
        // Realtor::factory(5)->create();
    }
}
