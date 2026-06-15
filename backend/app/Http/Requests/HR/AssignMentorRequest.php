<?php

namespace App\Http\Requests\HR;

use App\Models\CompanyMember;
use Illuminate\Foundation\Http\FormRequest;

class AssignMentorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handled by ApplicationPolicy@update
    }

    public function rules(): array
    {
        $company = app('current_company');

        return [
            'mentor_user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($company) {
                    $isMember = CompanyMember::where('company_id', $company->id)
                        ->where('user_id', $value)
                        ->where('role', 'mentor')
                        ->where('is_active', true)
                        ->exists();

                    if (! $isMember) {
                        $fail('User yang dipilih bukan mentor aktif di perusahaan ini.');
                    }
                },
            ],
        ];
    }
}
