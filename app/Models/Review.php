<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_request_id',
        'client_id',
        'provider_id',
        'rating',
        'comment',
        'is_moderated',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_moderated' => 'boolean',
    ];

    // Relations

    /**
     * Get the service request this review belongs to.
     */
    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Get the client (user) who wrote this review.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the provider (user) who received this review.
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Scope a query to only include moderated reviews.
     */
    public function scopeModerated($query)
    {
        return $query->where('is_moderated', true);
    }

    /**
     * Scope a query to only include unmoderated reviews.
     */
    public function scopeUnmoderated($query)
    {
        return $query->where('is_moderated', false);
    }
}
