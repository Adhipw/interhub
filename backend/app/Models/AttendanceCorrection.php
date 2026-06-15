<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'attendance_id',
        'requested_by',
        'new_check_in_at',
        'new_check_out_at',
        'reason',
        'status',
        'reviewed_by',
        'reviewer_notes',
    ];

    protected $casts = [
        'new_check_in_at' => 'datetime',
        'new_check_out_at' => 'datetime',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
