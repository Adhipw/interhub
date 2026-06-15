<?php

namespace App\Models;

use App\Services\LocationService;
use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    protected $fillable = [
        'user_id',
        'event_type',
        'severity',
        'description',
        'payload',
        'ip_address',
        'user_agent',
        'region',
    ];

    protected $appends = ['created_at_human'];

    protected static function booted()
    {
        // static::creating(function ($model) {
        //     if ($model->ip_address && ! $model->region) {
        //         $model->region = LocationService::getRegion($model->ip_address);
        //     }
        // });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
