<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class SearchInternshipDTO
{
    public function __construct(
        public readonly ?string $q = null,
        public readonly ?string $location = null,
        public readonly ?string $type = null,
        public readonly ?string $isPaid = null,
        public readonly ?string $sort = 'latest',
        public readonly int $perPage = 12
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            q: $request->input('q', $request->input('search')),
            location: $request->input('location'),
            type: $request->input('type'),
            isPaid: $request->input('is_paid'),
            sort: $request->input('sort', 'latest'),
            perPage: (int) $request->input('per_page', 12)
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'q' => $this->q,
            'location' => $this->location,
            'type' => $this->type,
            'is_paid' => $this->isPaid,
            'sort' => $this->sort,
        ]);
    }
}
