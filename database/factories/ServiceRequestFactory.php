<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'client_id', 'provider_id', 'service_id', 'category_id' will be set in the seeder
            'description' => fake()->paragraph(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'proposed_budget' => fake()->optional()->randomFloat(2, 10, 300),
            'desired_date_time' => fake()->dateTimeBetween('+1 day', '+2 months'),
            'status' => fake()->randomElement(['pending', 'accepted', 'refused', 'in_progress', 'completed', 'cancelled_by_client']),
        ];
    }
}
