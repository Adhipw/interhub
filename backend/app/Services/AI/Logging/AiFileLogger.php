<?php

namespace App\Services\AI\Logging;

use App\Models\AiFileAccessLog;
use Illuminate\Support\Facades\Auth;

class AiFileLogger
{
    public function logAccess(string $filePath, string $fileType, string $feature, string $purpose): void
    {
        AiFileAccessLog::create([
            'user_id' => Auth::id(),
            'file_path' => $filePath,
            'file_type' => $fileType,
            'ai_feature' => $feature,
            'purpose' => $purpose,
            'accessed_at' => now(),
        ]);
    }
}
