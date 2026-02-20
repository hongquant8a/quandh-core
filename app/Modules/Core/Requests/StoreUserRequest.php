<?php

namespace App\Modules\Core\Requests;

use App\Modules\Core\Enums\UserStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'status'   => ['nullable', 'in:' . implode(',', UserStatusEnum::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên người dùng không được để trống.',
            'name.string'   => 'Tên người dùng phải là một chuỗi ký tự.',
            'name.max'      => 'Tên người dùng không được vượt quá 255 ký tự.',
            'email.required' => 'Email không được để trống.',
            'email.email'   => 'Email không hợp lệ.',
            'email.unique'  => 'Email đã tồn tại.',
            'password.required'  => 'Mật khẩu không được để trống.',
            'password.string'    => 'Mật khẩu phải là một chuỗi ký tự.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu không khớp.',
            'status.in' => 'Trạng thái không hợp lệ. Chỉ chấp nhận active, inactive, banned.',
        ];
    }
}
