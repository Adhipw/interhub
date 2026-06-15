<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class InternshipResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            'type' => $this->type,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_paid' => $this->is_paid,
            'stipend' => $this->stipend,
            'deadline_at' => $this->deadline_at?->format('Y-m-d'),
            'deadline_at_human' => $this->deadline_at?->diffForHumans(),
            'status' => $this->status,
            'tags' => $this->tags,
            'created_at' => $this->created_at?->toIso8601String(),
            'created_at_human' => $this->created_at?->diffForHumans(),

            // Relationships
            'company' => new CompanyResource($this->whenLoaded('company')),
        ];
    }
}
