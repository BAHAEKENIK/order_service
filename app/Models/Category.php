<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Pour le slug

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_path',
    ];

    /**
     * Boot the model.
     * Automatically set the slug when creating/updating if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relations

    /**
     * Get the services belonging to this category.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the service requests belonging to this category.
     */
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
