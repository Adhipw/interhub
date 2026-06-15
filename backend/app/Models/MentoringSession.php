<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentoringSession extends Model
{
    protected $fillable = [
        'application_id',
        'mentor_user_id',
        'title',
        'description',
        'scheduled_at',
        'duration_minutes',
        'status',
        'meeting_link',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_user_id');
    }
}
