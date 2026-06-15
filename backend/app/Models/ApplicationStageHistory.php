<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStageHistory extends Model
{
    protected $table = 'application_stage_history';

    protected $fillable = [
        'application_id',
        'from_stage_id',
        'to_stage_id',
        'changed_by',
        'notes',
        'duration_minutes',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function fromStage()
    {
        return $this->belongsTo(RecruitmentStage::class, 'from_stage_id');
    }

    public function toStage()
    {
        return $this->belongsTo(RecruitmentStage::class, 'to_stage_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
