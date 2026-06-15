<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Policy handles this, but we can double check scope here
        $application = $this->route('application');
        $company = app('current_company');

        if (Auth::user()->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        return $application->internship->company_id === $company->id;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,reviewing,accepted,rejected',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status tidak valid.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
