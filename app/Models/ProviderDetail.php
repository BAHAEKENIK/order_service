<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject; // Pour une meilleure manipulation du JSON

class ProviderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'professional_description',
        'certificates',
        'average_rating',
        'is_available',
    ];

    protected $casts = [
        'certificates' => AsArrayObject::class, // Ou 'array' si vous préférez un simple tableau PHP
        'is_available' => 'boolean',
        'average_rating' => 'decimal:1',
    ];

    // Relations

    /**
     * Get the user (provider) that owns these details.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
