<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'phone_number',
        'address',
        'education',
        'skills',
        'cv_path',
        'portfolio_path',
        'ai_consent',
        'ai_consent_updated_at',
    ];

    protected $casts = [
        'education' => 'array',
        'skills' => 'array',
        'ai_consent' => 'boolean',
    ];

    protected $appends = ['cv_url', 'portfolio_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCvUrlAttribute(): ?string
    {
        if (! $this->cv_path) {
            return null;
        }

        return URL::temporarySignedRoute(
            'storage.private',
            now()->addMinutes(30),
            ['type' => 'cvs', 'filename' => basename($this->cv_path)]
        );
    }

    public function getPortfolioUrlAttribute(): ?string
    {
        if (! $this->portfolio_path) {
            return null;
        }

        return URL::temporarySignedRoute(
            'storage.private',
            now()->addMinutes(30),
            ['type' => 'portfolios', 'filename' => basename($this->portfolio_path)]
        );
    }
}
