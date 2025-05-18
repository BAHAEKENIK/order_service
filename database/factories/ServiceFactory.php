<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'user_id' (provider) and 'category_id' will be set in the seeder
            'title' => Str::title(fake()->words(rand(3, 6), true)),
            'description' => fake()->paragraphs(3, true),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'base_price' => fake()->randomFloat(2, 20, 500),
            'image_path' => null, // Or fake()->imageUrl(640, 480, 'services')
            'status' => fake()->randomElement(['available', 'unavailable']),
        ];
    }
}
