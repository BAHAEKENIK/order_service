<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

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
        'certificates' => AsArrayObject::class,
        'is_available' => 'boolean',
        'average_rating' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
