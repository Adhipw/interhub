<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'description',
        'website',
        'location',
        'is_verified',
    ];

    protected $appends = ['created_at_human'];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(CompanyMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_members')
            ->withPivot('role', 'is_active')
            ->withTimestamps();
    }

    public function getCreatedAtHumanAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }
}
