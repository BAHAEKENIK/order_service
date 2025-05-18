<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\ProviderDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderDetailFactory extends Factory
{
    public function definition(): array
    {
        $certificates = [];
        for ($i = 0; $i < rand(0, 3); $i++) {
            $certificates[] = [
                'name' => 'Certificate of ' . fake()->bs(),
                'file_url' => 'path/to/fake_cert_' . Str::random(8) . '.pdf',
                'issued_date' => fake()->date(),
            ];
        }

        return [
            // 'user_id' will be set in the seeder or via relationship
            'professional_description' => fake()->paragraphs(2, true),
            'certificates' => $certificates,
            'average_rating' => fake()->optional(0.7, 0)->randomFloat(1, 1, 5), // 70% chance of having a rating
            'is_available' => fake()->boolean(80), // 80% chance of being available
        ];
    }
}
