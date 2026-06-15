<?php

namespace App\Http\Requests\Nearby;

use Illuminate\Foundation\Http\FormRequest;

class AiRecommendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'radius' => 'nullable|integer|max:50',
            'prompt' => 'nullable|string|max:500',
        ];
    }
}
