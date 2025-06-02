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

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function scopeModerated($query)
    {
        return $query->where('is_moderated', true);
    }

    public function scopeUnmoderated($query)
    {
        return $query->where('is_moderated', false);
    }
}
