<?php

namespace Database\Factories;

use App\Models\ContactUsMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Added Str for admin_notes if needed

class ContactUsMessageFactory extends Factory
{
    protected $model = ContactUsMessage::class; // Good practice to specify the model

    public function definition(): array
    {
        $status = fake()->randomElement(['new', 'read_by_admin', 'replied']);
        $adminNotes = null;
        $adminReply = null;
        $repliedAt = null;

        if ($status === 'read_by_admin') {
            $adminNotes = 'Viewed by admin on ' . fake()->dateTimeThisMonth()->format('Y-m-d');
        } elseif ($status === 'replied') {
            $adminNotes = 'Viewed by admin on ' . fake()->dateTimeThisMonth(now()->subDay())->format('Y-m-d'); // Viewed before reply
            $adminReply = fake()->paragraph(2);
            $repliedAt = fake()->dateTimeBetween('-1 week', 'now'); // Replied sometime in the last week
             // Prepend reply info to admin_notes for history
            $adminNotes .= "\nReplied on " . $repliedAt->format('Y-m-d H:i') . ":\n" . Str::limit($adminReply, 100);
        }


        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => Str::limit(fake()->sentence(rand(3,6)), 250), // Limit subject length
            'message' => fake()->paragraphs(rand(2, 4), true),
            'user_id' => fake()->optional(0.3)->randomElement(User::where('role', 'client')->pluck('id')->toArray()), // 30% chance it's from a logged-in client
            'status' => $status,
            'admin_notes' => $adminNotes,
            'admin_reply' => $adminReply,    // ADDED
            'replied_at' => $repliedAt,     // ADDED
        ];
    }

    /**
     * Indicate that the message is new.
     */
    public function newStatus(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'new',
                'admin_reply' => null,
                'replied_at' => null,
            ];
        });
    }

    /**
     * Indicate that the message has been read by admin.
     */
    public function readByAdmin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'read_by_admin',
                'admin_reply' => null,
                'replied_at' => null,
                 'admin_notes' => 'Viewed by admin on ' . now()->subHours(rand(1,24))->format('Y-m-d H:i'),
            ];
        });
    }

    /**
     * Indicate that the message has been replied to.
     */
    public function replied(): Factory
    {
        return $this->state(function (array $attributes) {
            $replyContent = fake()->paragraph(2);
            return [
                'status' => 'replied',
                'admin_reply' => $replyContent,
                'replied_at' => now()->subMinutes(rand(5, 500)),
                'admin_notes' => "Viewed by admin. \nReplied on " . now()->subMinutes(rand(5, 500))->format('Y-m-d H:i') . ":\n" . Str::limit($replyContent, 100),
            ];
        });
    }
}
