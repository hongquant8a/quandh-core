<?php

namespace App\Modules\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'guard_name' => 'nullable|string|max:255',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên vai trò không được để trống.',
        ];
    }
    public function attributes(): array
    {
        return [
            'name' => 'Tên',
            'guard_name' => 'guard name',
            'permission_ids' => 'permission ids',
            'permission_ids.*' => 'Phần tử của permission ids',
        ];
    }
    public function bodyParameters(): array
    {
        return [];
    }
}
