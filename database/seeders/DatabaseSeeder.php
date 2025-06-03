<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Message;
use App\Models\ProviderDetail;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\ContactUsMessage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'bahaekenik@gmail.com'],
            [
                'name' => 'Bahae Kenikssi',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('Admin user checked/created.');

        $clients = User::factory()->client()->count(10)->create();
        $this->command->info('10 Client users created.');

        $providers = User::factory()->provider()->count(10)->create();
        $providers->each(function ($provider) {
            ProviderDetail::factory()->create(['user_id' => $provider->id]);
        });
        $this->command->info('10 Provider users with details created.');

        $categoriesData = [
            ['name' => 'Home Cleaning', 'slug' => 'home-cleaning', 'description' => 'Professional home cleaning services.'],
            ['name' => 'Plumbing', 'slug' => 'plumbing', 'description' => 'Fixing leaks, installations, and more.'],
            ['name' => 'Electrical Works', 'slug' => 'electrical-works', 'description' => 'Wiring, repairs, and installations.'],
            ['name' => 'Tutoring', 'slug' => 'tutoring', 'description' => 'Academic help for various subjects.'],
            ['name' => 'Moving Services', 'slug' => 'moving-services', 'description' => 'Relocation and moving assistance.'],
            ['name' => 'Gardening', 'slug' => 'gardening', 'description' => 'Lawn care and garden maintenance.'],
            ['name' => 'Admin Support', 'slug' => 'admin-support', 'description' => 'For administrative communications.'],
            ['name' => 'Admin Direct User Chat', 'slug' => 'admin-direct-user-chat', 'description' => 'For direct chats initiated by administrators.']
        ];
        $categories = collect();
        foreach ($categoriesData as $catData) {
            $categories->push(Category::firstOrCreate(
                ['slug' => $catData['slug']],
                $catData
            ));
        }
        $this->command->info(count($categoriesData) . ' Categories checked/created.');

        $allServices = collect();
        if ($categories->isNotEmpty() && $providers->isNotEmpty()) {
            $providers->each(function ($provider) use ($categories, &$allServices) {
                for ($i = 0; $i < rand(1, 3); $i++) {
                    $categoryForService = $categories->whereNotIn('slug', ['admin-support', 'admin-direct-user-chat'])->random();
                    if($categoryForService) {
                         $allServices->push(Service::factory()->create([
                            'user_id' => $provider->id,
                            'category_id' => $categoryForService->id,
                        ]));
                    }
                }
            });
        }
        $this->command->info($allServices->count() . ' Provider Services created.');

        $serviceRequests = collect();
        if ($clients->isNotEmpty() && $providers->isNotEmpty() && $allServices->whereNotIn('category.slug', ['admin-support', 'admin-direct-user-chat'])->isNotEmpty()) {
            for ($i = 0; $i < 30; $i++) {
                $client = $clients->random();
                $provider = $providers->random();

                $providerService = $allServices->where('user_id', $provider->id)->filter(function($service) {
                    return !in_array($service->category->slug, ['admin-support', 'admin-direct-user-chat']);
                })->random();

                if ($providerService) {
                    $serviceRequests->push(ServiceRequest::factory()->create([
                        'client_id' => $client->id,
                        'provider_id' => $provider->id,
                        'service_id' => $providerService->id,
                        'category_id' => $providerService->category_id,
                    ]));
                } else {
                    $randomCategory = $categories->whereNotIn('slug', ['admin-support', 'admin-direct-user-chat'])->random();
                    if($randomCategory){
                        $serviceRequests->push(ServiceRequest::factory()->create([
                            'client_id' => $client->id,
                            'provider_id' => $provider->id,
                            'service_id' => null,
                            'category_id' => $randomCategory->id,
                        ]));
                    }
                }
            }
            $this->command->info($serviceRequests->count() . ' Service Requests created.');
        } else {
            $this->command->warn('Skipping Service Requests: Not enough clients, providers, or suitable services.');
        }

        if ($serviceRequests->count() >= 5) {
            $serviceRequests->random(min(5, $serviceRequests->count()))->each(function ($sr) {
                $sr->update(['status' => 'completed']);
            });
        }

        $completedRequests = ServiceRequest::where('status', 'completed')->get();
        $reviews = collect();
        if ($completedRequests->isNotEmpty()) {
            $completedRequests->each(function ($request) use (&$reviews) {
                if (!$request->review()->exists()) {
                    $reviews->push(Review::factory()->create([
                        'service_request_id' => $request->id,
                        'client_id' => $request->client_id,
                        'provider_id' => $request->provider_id,
                    ]));
                }
            });
            $this->command->info($reviews->count() . ' Reviews created for completed requests.');

            $providersWithReviews = User::whereHas('reviewsReceived')->with('providerDetail')->get();
            foreach ($providersWithReviews as $providerUser) {
                $avgRating = $providerUser->reviewsReceived()->avg('rating');
                if ($providerUser->providerDetail) {
                    $providerUser->providerDetail->update(['average_rating' => round($avgRating, 1)]);
                }
            }
            $this->command->info('Provider average ratings updated.');
        } else {
            $this->command->warn('Skipping Reviews: No completed service requests found.');
        }

        $messageableRequests = $serviceRequests->whereIn('status', ['accepted', 'in_progress', 'inquiry'])->take(15);
        if ($messageableRequests->isNotEmpty()) {
            $messageableRequests->each(function ($request) {
                $userIsAdminChat = ($request->client->isAdmin() || $request->provider->isAdmin()) && Str::contains($request->description, 'Admin');
                $messageCount = $userIsAdminChat ? rand(1,3) : rand(2, 5);

                for ($j = 0; $j < $messageCount; $j++) {
                    $isClientTheSender = fake()->boolean();
                    $senderId = $isClientTheSender ? $request->client_id : $request->provider_id;
                    $receiverId = $isClientTheSender ? $request->provider_id : $request->client_id;

                    Message::factory()->create([
                        'service_request_id' => $request->id,
                        'sender_id' => $senderId,
                        'receiver_id' => $receiverId,
                        'created_at' => fake()->dateTimeBetween($request->created_at, 'now'),
                    ]);
                }
            });
            $this->command->info($messageableRequests->count() * rand(2,3) . ' (approx) Messages created for requests.');
        } else {
            $this->command->warn('Skipping Messages: No suitable (accepted/in_progress/inquiry) service requests found.');
        }

        ContactUsMessage::factory()->count(3)->newStatus()->create();
        ContactUsMessage::factory()->count(2)->readByAdmin()->create();
        ContactUsMessage::factory()->count(2)->replied()->create();
        if ($clients->isNotEmpty()) {
            ContactUsMessage::factory()->count(2)->create(['user_id' => $clients->random()->id, 'status' => 'new']);
            ContactUsMessage::factory()->count(1)->replied()->create(['user_id' => $clients->random()->id]);
        }
        $this->command->info('Contact Us Messages seeded.');

        $this->command->info('Database seeding completed successfully!');
    }
}
