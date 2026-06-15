<?php

namespace App\Http\Requests\Nearby;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|integer|min:1|max:50',
        ];
    }
}
