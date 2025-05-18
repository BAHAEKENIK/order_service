<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Message;
use App\Models\ProviderDetail;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // For re-seeding, it's good to disable foreign key checks and truncate
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // User::truncate();
        // Category::truncate();
        // ProviderDetail::truncate();
        // Service::truncate();
        // ServiceRequest::truncate();
        // Review::truncate();
        // Message::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Create Admin User
        $admin = User::factory()->admin()->create([
            'name' => 'Baha eddine Kenioua',
            'email' => 'bahaekenik@gmail.com',
            'password' => Hash::make('password'), // Change this to a secure password
        ]);
        $this->command->info('Admin user created.');

        // 2. Create Client Users
        $clients = User::factory()->client()->count(10)->create();
        $this->command->info('10 Client users created.');

        // 3. Create Provider Users and their ProviderDetails
        $providers = User::factory()->provider()->count(10)->create()->each(function ($provider) {
            ProviderDetail::factory()->create(['user_id' => $provider->id]);
        });
        $this->command->info('10 Provider users with details created.');

        // 4. Create Categories
        $categoriesData = [
            ['name' => 'Home Cleaning', 'description' => 'Professional home cleaning services.'],
            ['name' => 'Plumbing', 'description' => 'Fixing leaks, installations, and more.'],
            ['name' => 'Electrical Works', 'description' => 'Wiring, repairs, and installations.'],
            ['name' => 'Tutoring', 'description' => 'Academic help for various subjects.'],
            ['name' => 'Moving Services', 'description' => 'Relocation and moving assistance.'],
            ['name' => 'Gardening', 'description' => 'Lawn care and garden maintenance.'],
        ];

        $categories = collect();
        foreach ($categoriesData as $catData) {
            $categories->push(Category::factory()->create([
                'name' => $catData['name'],
                'slug' => Str::slug($catData['name']),
                'description' => $catData['description'],
            ]));
        }
        $this->command->info(count($categoriesData) . ' Categories created.');

        // 5. Create Services for Providers
        $services = collect();
        $providers->each(function ($provider) use ($categories, &$services) {
            // Each provider offers 1 to 3 services
            for ($i = 0; $i < rand(1, 3); $i++) {
                $services->push(Service::factory()->create([
                    'user_id' => $provider->id,
                    'category_id' => $categories->random()->id,
                ]));
            }
        });
        $this->command->info($services->count() . ' Services created.');

        // 6. Create Service Requests
        $serviceRequests = collect();
        if ($clients->isNotEmpty() && $providers->isNotEmpty() && $services->isNotEmpty()) {
            for ($i = 0; $i < 30; $i++) { // Create 30 service requests
                $client = $clients->random();
                $provider = $providers->random();
                // Ensure the chosen service belongs to the chosen provider
                $providerService = $services->where('user_id', $provider->id)->random();

                if ($providerService) {
                    $serviceRequests->push(ServiceRequest::factory()->create([
                        'client_id' => $client->id,
                        'provider_id' => $provider->id,
                        'service_id' => $providerService->id,
                        'category_id' => $providerService->category_id, // Match category with service
                    ]));
                }
            }
            $this->command->info($serviceRequests->count() . ' Service Requests created.');
        } else {
            $this->command->warn('Skipping Service Requests: Not enough clients, providers, or services.');
        }


        // 7. Create Reviews for 'completed' Service Requests
        $completedRequests = $serviceRequests->where('status', 'completed');
        $reviews = collect();
        if ($completedRequests->isNotEmpty()) {
            $completedRequests->each(function ($request) use (&$reviews) {
                // Only one review per completed request
                if (!Review::where('service_request_id', $request->id)->exists()) {
                    $reviews->push(Review::factory()->create([
                        'service_request_id' => $request->id,
                        'client_id' => $request->client_id,
                        'provider_id' => $request->provider_id,
                    ]));
                }
            });
            $this->command->info($reviews->count() . ' Reviews created for completed requests.');

            // Update provider average ratings based on new reviews
            $providersWithReviews = User::whereIn('id', $reviews->pluck('provider_id')->unique())->get();
            foreach ($providersWithReviews as $provider) {
                $avgRating = Review::where('provider_id', $provider->id)->avg('rating');
                if ($provider->providerDetail) {
                    $provider->providerDetail->update(['average_rating' => round($avgRating, 1)]);
                }
            }
            $this->command->info('Provider average ratings updated.');

        } else {
            $this->command->warn('Skipping Reviews: No completed service requests found.');
        }

        // 8. Create Messages for Service Requests
        $messages = collect();
        if ($serviceRequests->isNotEmpty()) {
            $serviceRequests->each(function ($request) use (&$messages) {
                // Create 2 to 5 messages per request
                for ($j = 0; $j < rand(2, 5); $j++) {
                    $isClientSender = fake()->boolean();
                    $messages->push(Message::factory()->create([
                        'service_request_id' => $request->id,
                        'sender_id' => $isClientSender ? $request->client_id : $request->provider_id,
                        'receiver_id' => $isClientSender ? $request->provider_id : $request->client_id,
                        'created_at' => fake()->dateTimeBetween($request->created_at, 'now'), // Messages after request creation
                    ]));
                }
            });
            $this->command->info($messages->count() . ' Messages created.');
        } else {
            $this->command->warn('Skipping Messages: No service requests found.');
        }

        // You can also seed ContactUsMessages if needed
        // ContactUsMessage::factory()->count(5)->create();
        // ContactUsMessage::factory()->count(2)->create(['user_id' => $clients->random()->id]);
        // $this->command->info('Contact Us Messages created.');

        $this->command->info('Database seeding completed successfully!');
    }
}
