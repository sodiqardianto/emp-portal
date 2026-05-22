<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'permission_code' => strtoupper(trim((string) $this->permission_code)),
            'is_active' => strtoupper(trim((string) ($this->is_active ?? 'Y'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'permission_code' => ['required', 'string', 'max:100', 'regex:/^[A-Z0-9_]+$/'],
            'permission_name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'in:Y,N'],
        ];
    }

    public function messages(): array
    {
        return [
            'permission_code.regex' => 'Permission code hanya boleh berisi huruf, angka, dan underscore.',
        ];
    }
}
