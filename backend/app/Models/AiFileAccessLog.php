<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiFileAccessLog extends Model
{
    protected $fillable = [
        'user_id',
        'file_path',
        'file_type',
        'ai_feature',
        'purpose',
        'accessed_at',
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
