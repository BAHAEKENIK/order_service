<?php

namespace Database\Factories;

use App\Models\ContactUsMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactUsMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => fake()->sentence(4),
            'message' => fake()->paragraph(3),
            'user_id' => null, // Can be linked to a user if sent by a logged-in user
            'status' => 'new',
            'admin_notes' => null,
        ];
    }
}
