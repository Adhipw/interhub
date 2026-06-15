<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateInternshipStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->hasRole('admin', 'super_admin');
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:draft,published,archived,flagged',
        ];
    }
}
