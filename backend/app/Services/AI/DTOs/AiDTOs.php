<?php

namespace App\Services\AI\DTOs;

use App\Services\AI\Enums\AiRole;

class AiMessage
{
    public function __construct(
        public AiRole $role,
        public string $content,
    ) {}

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'content' => $this->content,
        ];
    }
}

class AiResponse
{
    public function __construct(
        public string $content,
        public array $metadata = [],
        public ?int $tokensUsed = null,
    ) {}
}
