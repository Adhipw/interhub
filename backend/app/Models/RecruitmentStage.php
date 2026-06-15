<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruitmentStage extends Model
{
    protected $fillable = [
        'internship_id',
        'name',
        'order',
        'type',
        'sla_days',
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'current_stage_id');
    }
}
