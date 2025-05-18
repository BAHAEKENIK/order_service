<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Si vous utilisez la vérification d'email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Si vous utilisez Sanctum pour les API

class User extends Authenticatable // implements MustVerifyEmail (si applicable)
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'address',
        'city',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10+
    ];

    // Relations

    /**
     * Get the provider details associated with the user (if they are a provider).
     */
    public function providerDetail()
    {
        return $this->hasOne(ProviderDetail::class);
    }

    /**
     * Get the services offered by the user (if they are a provider).
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'user_id'); // 'user_id' est la clé étrangère dans la table services
    }

    /**
     * Get the service requests made by the user (as a client).
     */
    public function clientServiceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'client_id');
    }

    /**
     * Get the service requests received by the user (as a provider).
     */
    public function providerServiceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'provider_id');
    }

    /**
     * Get the reviews written by the user (as a client).
     */
    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'client_id');
    }

    /**
     * Get the reviews received by the user (as a provider).
     */
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'provider_id');
    }

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the messages received by the user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get the contact us messages sent by the user (if logged in).
     */
    public function contactUsMessages()
    {
        return $this->hasMany(ContactUsMessage::class, 'user_id');
    }

    // Helper methods for roles
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
