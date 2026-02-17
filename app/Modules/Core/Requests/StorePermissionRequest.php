<?php

namespace App\Modules\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'guard_name' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên quyền không được để trống.',
            'name.max'      => 'Tên quyền không được vượt quá 255 ký tự.',
        ];
    }
}
