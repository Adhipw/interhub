<?php

namespace App\Services\AI\Enums;

enum AiRole: string
{
    case SYSTEM = 'system';
    case USER = 'user';
    case ASSISTANT = 'assistant';
}
