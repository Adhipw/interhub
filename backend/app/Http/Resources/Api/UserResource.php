<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar_url' => $this->avatar_url,
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),
            'role' => $this->role,
            'all_roles' => $this->all_roles,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'detail' => $this->whenLoaded('detail'),
        ];
    }
}
