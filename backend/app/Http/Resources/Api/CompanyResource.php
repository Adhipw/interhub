<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'logo_url' => $this->logo_url,
            'website' => $this->website,
            'industry' => $this->industry,
            'location' => $this->location,
            'is_verified' => $this->is_verified,
            'internships_count' => $this->internships_count,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
