<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class ExternalIntegration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'provider',
        'credentials',
        'settings',
        'is_active',
        'is_secret',
        'last_synced_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_secret' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Get the credentials.
     *
     * @param  string  $value
     * @return array
     */
    public function getCredentialsAttribute($value)
    {
        if (! $value) {
            return [];
        }
        try {
            return json_decode(Crypt::decryptString($value), true);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Set the credentials.
     *
     * @param  array  $value
     * @return void
     */
    public function setCredentialsAttribute($value)
    {
        $this->attributes['credentials'] = Crypt::encryptString(json_encode($value));
    }

    public function logs(): HasMany
    {
        return $this->hasMany(IntegrationLog::class);
    }
}
