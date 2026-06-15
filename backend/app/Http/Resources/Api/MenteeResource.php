<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenteeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar_url' => $this->user->avatar_url,
                'detail' => $this->user->detail,
            ],
            'internship' => [
                'id' => $this->internship->id,
                'title' => $this->internship->title,
                'type' => $this->internship->type,
            ],
            'cover_letter' => $this->cover_letter,
            'created_at' => $this->created_at,
            'feedbacks' => MentorFeedbackResource::collection($this->whenLoaded('mentorFeedbacks')),
            'tasks' => MentorTaskResource::collection($this->whenLoaded('tasks')),
            'sessions' => MentoringSessionResource::collection($this->whenLoaded('mentoringSessions')),
        ];
    }
}
