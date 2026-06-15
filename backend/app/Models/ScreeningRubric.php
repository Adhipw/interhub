<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreeningRubric extends Model
{
    protected $fillable = [
        'internship_id',
        'criteria',
    ];

    protected $casts = [
        'criteria' => 'array',
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }
}
