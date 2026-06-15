<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'internship_id',
        'status',
        'current_stage_id',
        'cover_letter',
        'timeline',
        'cv_snapshot',
        'portfolio_snapshot',
        'interviewer_id',
        'mentor_user_id',
        'hr_notes',
    ];

    protected $appends = ['created_at_human'];

    protected $casts = [
        'timeline' => 'array',
    ];

    public function currentStage(): BelongsTo
    {
        return $this->belongsTo(RecruitmentStage::class, 'current_stage_id');
    }

    public function stageHistory(): HasMany
    {
        return $this->hasMany(ApplicationStageHistory::class);
    }

    public function score(): HasOne
    {
        return $this->hasOne(ApplicationScore::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function interviewSchedules(): HasMany
    {
        return $this->hasMany(InterviewSchedule::class);
    }

    public function mentorFeedbacks(): HasMany
    {
        return $this->hasMany(MentorFeedback::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_user_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(MentorTask::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(MentorEvaluation::class);
    }

    public function mentoringSessions(): HasMany
    {
        return $this->hasMany(MentoringSession::class);
    }

    public function getCreatedAtHumanAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ApplicationMessage::class)->latest();
    }

    public function onboardingDocuments(): HasMany
    {
        return $this->hasMany(OnboardingDocument::class);
    }
}
