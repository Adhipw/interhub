<?php

namespace App\Services\AI\Enums;

enum AiModel: string
{
    case GEMINI_PRO = 'gemini-1.5-pro';
    case GEMINI_FLASH = 'gemini-1.5-flash';
    case LOCAL_LLAMA = 'llama3';
    case FAKE = 'fake';
}
