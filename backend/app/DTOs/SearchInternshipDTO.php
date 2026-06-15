<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class SearchInternshipDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $location = null,
        public readonly ?string $type = null,
        public readonly int $perPage = 12
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->input('search'),
            location: $request->input('location'),
            type: $request->input('type'),
            perPage: (int) $request->input('per_page', 12)
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'location' => $this->location,
            'type' => $this->type,
        ]);
    }
}
