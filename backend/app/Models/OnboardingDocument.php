<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingDocument extends Model
{
    protected $fillable = [
        'application_id',
        'user_id',
        'type',
        'file_path',
        'file_name',
        'status',
        'notes',
        'verified_by',
        'verified_at',
    ];

    const STATUS_PENDING = 'pending';

    const STATUS_VERIFIED = 'verified';

    const STATUS_REJECTED = 'rejected';

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
