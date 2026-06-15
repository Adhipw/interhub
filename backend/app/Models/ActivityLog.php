<?php

namespace App\Models;

use App\Services\LocationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
        'region',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    protected static function booted()
    {
        // static::creating(function ($model) {
        //     if ($model->ip_address && ! $model->region) {
        //         $model->region = LocationService::getRegion($model->ip_address);
        //     }
        // });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
