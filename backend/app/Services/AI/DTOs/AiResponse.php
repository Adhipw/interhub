<?php

namespace App\Services\AI\DTOs;

class AiResponse
{
    public function __construct(
        public string $content,
        public array $metadata = [],
        public ?int $tokensUsed = null,
    ) {}
}
