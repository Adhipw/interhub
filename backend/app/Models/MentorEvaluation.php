<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorEvaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'mentor_user_id',
        'title',
        'summary',
        'metrics',
        'recommendation',
        'final_status',
    ];

    protected $casts = [
        'metrics' => 'array',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_user_id');
    }
}
