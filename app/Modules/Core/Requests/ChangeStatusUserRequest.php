<?php

namespace App\Modules\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:active,inactive,banned',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in'       => 'Trạng thái không hợp lệ. Chỉ chấp nhận active, inactive, banned.',
        ];
    }
}
