<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Message;
use App\Models\ProviderDetail;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\ContactUsMessage; // ADD THIS LINE
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Only if using DB::statement
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // For re-seeding, it's good to disable foreign key checks and truncate tables
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // For MySQL
        // // For SQLite, this is usually not needed or handled differently.
        // // For PostgreSQL: ALTER TABLE your_table_name DISABLE TRIGGER ALL;

        // // Truncate tables (order might matter depending on foreign key constraints)
        // // Make sure to re-enable checks after seeding
        // ContactUsMessage::truncate();
        // Message::truncate();
        // Review::truncate();
        // ServiceRequest::truncate();
        // Service::truncate();
        // ProviderDetail::truncate();
        // Category::truncate();
        // User::where('email', '!=', 'bahaekenik@gmail.com')->delete(); // Keep specific admin if re-running on existing DB


        // 1. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'bahaekenik@gmail.com'],
            [
                'name' => 'Baha eddine Kenioua',
                'password' => Hash::make('password'), // Change this in production!
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('Admin user checked/created.');

        // 2. Create Client Users
        $clients = User::factory()->client()->count(10)->create();
        $this->command->info('10 Client users created.');

        // 3. Create Provider Users and their ProviderDetails
        $providers = User::factory()->provider()->count(10)->create();
        $providers->each(function ($provider) {
            ProviderDetail::factory()->create(['user_id' => $provider->id]);
        });
        $this->command->info('10 Provider users with details created.');

        // 4. Create Categories
        $categoriesData = [
            ['name' => 'Home Cleaning', 'slug' => 'home-cleaning', 'description' => 'Professional home cleaning services.'],
            ['name' => 'Plumbing', 'slug' => 'plumbing', 'description' => 'Fixing leaks, installations, and more.'],
            ['name' => 'Electrical Works', 'slug' => 'electrical-works', 'description' => 'Wiring, repairs, and installations.'],
            ['name' => 'Tutoring', 'slug' => 'tutoring', 'description' => 'Academic help for various subjects.'],
            ['name' => 'Moving Services', 'slug' => 'moving-services', 'description' => 'Relocation and moving assistance.'],
            ['name' => 'Gardening', 'slug' => 'gardening', 'description' => 'Lawn care and garden maintenance.'],
            ['name' => 'Admin Support', 'slug' => 'admin-support', 'description' => 'For administrative communications.'], // For admin chats
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

        // 5. Create Services for Providers
        $allServices = collect(); // Renamed from $services to avoid conflict
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

        // 6. Create Service Requests
        $serviceRequests = collect();
        if ($clients->isNotEmpty() && $providers->isNotEmpty() && $allServices->whereNotIn('category.slug', ['admin-support', 'admin-direct-user-chat'])->isNotEmpty()) {
            for ($i = 0; $i < 30; $i++) {
                $client = $clients->random();
                $provider = $providers->random();

                // Try to get a service specific to this provider
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
                } else { // Fallback if provider has no suitable services (less likely now)
                    $randomCategory = $categories->whereNotIn('slug', ['admin-support', 'admin-direct-user-chat'])->random();
                    if($randomCategory){
                        $serviceRequests->push(ServiceRequest::factory()->create([
                            'client_id' => $client->id,
                            'provider_id' => $provider->id,
                            'service_id' => null, // General request
                            'category_id' => $randomCategory->id,
                        ]));
                    }
                }
            }
            $this->command->info($serviceRequests->count() . ' Service Requests created.');
        } else {
            $this->command->warn('Skipping Service Requests: Not enough clients, providers, or suitable services.');
        }


        // 7. Create Reviews for 'completed' Service Requests
        // First, ensure some requests are marked as completed
        if ($serviceRequests->count() >= 5) { // Ensure there are enough requests
            $serviceRequests->random(min(5, $serviceRequests->count()))->each(function ($sr) {
                $sr->update(['status' => 'completed']);
            });
        }

        $completedRequests = ServiceRequest::where('status', 'completed')->get(); // Re-fetch completed ones
        $reviews = collect();
        if ($completedRequests->isNotEmpty()) {
            $completedRequests->each(function ($request) use (&$reviews) {
                if (!$request->review()->exists()) { // Check if review exists for THIS request
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

        // 8. Create Messages for some Service Requests
        $messageableRequests = $serviceRequests->whereIn('status', ['accepted', 'in_progress', 'inquiry'])->take(15);
        if ($messageableRequests->isNotEmpty()) {
            $messageableRequests->each(function ($request) {
                $userIsAdminChat = ($request->client->isAdmin() || $request->provider->isAdmin()) && Str::contains($request->description, 'Admin');
                $messageCount = $userIsAdminChat ? rand(1,3) : rand(2, 5); // Fewer messages for admin seed chats

                for ($j = 0; $j < $messageCount; $j++) {
                    $isClientTheSender = fake()->boolean();
                     // If SR involves admin, admin can also be a sender/receiver
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
            $this->command->info($messageableRequests->count() * rand(2,3) . ' (approx) Messages created for requests.'); // Approx count
        } else {
            $this->command->warn('Skipping Messages: No suitable (accepted/in_progress/inquiry) service requests found.');
        }

        // 9. Seed ContactUsMessages (NEW SECTION)
        ContactUsMessage::factory()->count(3)->newStatus()->create(); // 3 New messages
        ContactUsMessage::factory()->count(2)->readByAdmin()->create(); // 2 Read by admin
        ContactUsMessage::factory()->count(2)->replied()->create();   // 2 Replied by admin
        // Create some from existing logged-in clients
        if ($clients->isNotEmpty()) {
            ContactUsMessage::factory()->count(2)->create(['user_id' => $clients->random()->id, 'status' => 'new']);
            ContactUsMessage::factory()->count(1)->replied()->create(['user_id' => $clients->random()->id]);
        }
        $this->command->info('Contact Us Messages seeded.');


        // DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // For MySQL
        // For PostgreSQL: ALTER TABLE your_table_name ENABLE TRIGGER ALL;
        $this->command->info('Database seeding completed successfully!');
    }
}
