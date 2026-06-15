<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'application_id' => 'required|exists:applications,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'consent' => 'required|accepted',
        ];
    }
}
