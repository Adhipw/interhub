<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'profile_photo' => $this->user->detail->profile_photo_url ?? null,
            ],
            'internship' => [
                'id' => $this->application->internship->id,
                'title' => $this->application->internship->title,
            ],
            'status' => $this->getStatus(),
            'check_in' => [
                'time' => $this->check_in_at?->toIso8601String(),
                'location' => [
                    'lat' => $this->check_in_lat,
                    'lng' => $this->check_in_lng,
                ],
                'note' => $this->check_in_note,
            ],
            'check_out' => [
                'time' => $this->check_out_at?->toIso8601String(),
                'location' => [
                    'lat' => $this->check_out_lat,
                    'lng' => $this->check_out_lng,
                ],
                'note' => $this->check_out_note,
            ],
            'duration' => $this->calculateDuration(),
        ];
    }

    protected function getStatus(): string
    {
        if (! $this->check_out_at) {
            return 'Active';
        }

        return 'Completed';
    }

    protected function calculateDuration(): ?string
    {
        if (! $this->check_in_at || ! $this->check_out_at) {
            return null;
        }

        return $this->check_in_at->diffForHumans($this->check_out_at, true);
    }
}
