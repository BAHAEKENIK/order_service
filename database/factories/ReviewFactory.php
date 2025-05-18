<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'service_request_id', 'client_id', 'provider_id' will be set in the seeder
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional()->sentence(),
            'is_moderated' => fake()->boolean(70), // 70% chance of being moderated
        ];
    }
}
