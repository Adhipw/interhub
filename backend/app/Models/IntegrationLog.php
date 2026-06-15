<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_integration_id',
        'status',
        'message',
        'items_processed',
        'items_imported',
        'items_skipped',
        'error_details',
    ];

    protected $casts = [
        'error_details' => 'array',
        'items_processed' => 'integer',
        'items_imported' => 'integer',
        'items_skipped' => 'integer',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(ExternalIntegration::class, 'external_integration_id');
    }
}
