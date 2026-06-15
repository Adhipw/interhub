<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Internship extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'title',
        'slug',
        'description',
        'requirements',
        'benefits',
        'type',
        'location',
        'latitude',
        'longitude',
        'is_paid',
        'stipend',
        'deadline_at',
        'status',
        'tags',
        'is_external',
        'external_source',
        'external_id',
        'external_url',
        'external_metadata',
    ];

    protected $appends = ['created_at_human'];

    protected $casts = [
        'deadline_at' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'requirements' => 'array',
        'benefits' => 'array',
        'tags' => 'array',
        'is_external' => 'boolean',
        'external_metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function savedByUsers(): HasMany
    {
        return $this->hasMany(SavedInternship::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getCreatedAtHumanAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }
}
