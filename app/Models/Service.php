<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Provider ID
        'category_id',
        'title',
        'description',
        'address',
        'city',
        'base_price',
        'image_path',
        'status',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
    ];

    // Relations

    /**
     * Get the provider (user) who offers this service.
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the category this service belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the service requests associated with this service.
     */
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
