<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'provider_id',
        'service_id',
        'category_id',
        'description',
        'address',
        'city',
        'proposed_budget',
        'desired_date_time',
        'status',
    ];

    protected $casts = [
        'proposed_budget' => 'decimal:2',
        'desired_date_time' => 'datetime',
    ];

    // Relations

    /**
     * Get the client (user) who made this request.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the provider (user) to whom this request is made.
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Get the specific service this request is for (if any).
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the category of this service request.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the review associated with this service request.
     */
    public function review()
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Get the messages associated with this service request.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
