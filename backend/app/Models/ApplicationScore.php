<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationScore extends Model
{
    protected $fillable = [
        'application_id',
        'score',
        'factors',
        'is_ai_suggested',
        'human_reviewed',
        'reviewer_id',
    ];

    protected $casts = [
        'factors' => 'array',
        'score' => 'float',
        'human_reviewed' => 'boolean',
        'is_ai_suggested' => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
