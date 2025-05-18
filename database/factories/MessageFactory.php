<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'service_request_id', 'sender_id', 'receiver_id' will be set in the seeder
            'content' => fake()->sentence(),
            'read_at' => fake()->optional(0.6, null)->dateTimeThisMonth(), // 60% chance of being read
        ];
    }
}
