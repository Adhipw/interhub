<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'message',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    protected $appends = ['created_at_human'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtHumanAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }
}
