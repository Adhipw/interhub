<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentorFeedbackResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'assessment' => $this->assessment,
            'status' => $this->status,
            'mentor' => [
                'id' => $this->mentor->id,
                'name' => $this->mentor->name,
            ],
            'created_at' => $this->created_at,
        ];
    }
}
