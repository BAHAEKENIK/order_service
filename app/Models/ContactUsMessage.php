<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUsMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_us_messages'; // Explicite car le nom du modèle est légèrement différent du pluriel de "contact_us_message"

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'user_id', // Nullable, for logged-in users
        'status',
        'admin_notes',
    ];

    // Relations

    /**
     * Get the user who sent the message (if logged in).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
