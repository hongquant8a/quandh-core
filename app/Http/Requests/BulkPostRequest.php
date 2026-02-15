<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:posts,id',
            'status' => 'required_if:action,update_status|in:draft,published,archived',
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Bạn chưa chọn bài viết nào.',
            'ids.*.exists' => 'Một trong các bài viết không tồn tại.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
