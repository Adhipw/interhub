<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->hasRole('admin', 'super_admin');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:locations,name',
            'type' => 'required|string|in:city,province,region',
        ];
    }
}
