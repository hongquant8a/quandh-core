<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'exists:users,id',
            'status' => 'required_if:action,update_status|in:active,inactive,banned',
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Danh sách người dùng không được để trống.',
            'ids.array' => 'Danh sách người dùng phải là một mảng.',
            'ids.min' => 'Danh sách người dùng phải có ít nhất 1 người dùng.',
        ];
    }
}
