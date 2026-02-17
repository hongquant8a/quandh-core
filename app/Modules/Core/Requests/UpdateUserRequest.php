<?php

namespace App\Modules\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $this->route('user'),
            'password' => 'sometimes|string|min:6|confirmed',
            'status'   => 'sometimes|in:active,inactive,banned',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string'   => 'Tên người dùng phải là một chuỗi ký tự.',
            'name.max'      => 'Tên người dùng không được vượt quá 255 ký tự.',
            'email.email'   => 'Email không hợp lệ.',
            'email.unique'  => 'Email đã tồn tại.',
            'password.string'    => 'Mật khẩu phải là một chuỗi ký tự.',
            'password.min'       => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu không khớp.',
            'status.in' => 'Trạng thái không hợp lệ. Chỉ chấp nhận active, inactive, banned.',
        ];
    }
}
