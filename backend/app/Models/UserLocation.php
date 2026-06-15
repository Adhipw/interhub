<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'latitude',
        'longitude',
        'is_primary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
