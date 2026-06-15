<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scheduled_at' => 'required|date|after:now',
            'type' => 'required|in:online,offline',
            'meeting_link' => 'required_if:type,online|nullable|url',
            'location' => 'required_if:type,offline|nullable|string',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
