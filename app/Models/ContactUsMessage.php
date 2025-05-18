<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUsMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_us_messages';

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'user_id',
        'status',
        'admin_notes',
        'admin_reply', // ADD THIS
        'replied_at',  // ADD THIS
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'replied_at' => 'datetime', // ADD THIS
    ];

    /**
     * Get the user who sent the message (if logged in).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
