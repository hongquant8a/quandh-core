<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'title'   => 'sometimes|string|max:255|unique:posts,title,' . $this->route('post'),
            'content' => 'sometimes|string|min:10',
            'status'  => 'sometimes|in:draft,published,archived',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string'   => 'Tiêu đề phải là một chuỗi ký tự.', 
            'title.max'      => 'Tiêu đề không được vượt quá 255 ký tự.',
            'title.unique'   => 'Tiêu đề bài viết đã tồn tại.',
            'content.string'   => 'Nội dung phải là một chuỗi ký tự.',
            'content.min'      => 'Nội dung phải có ít nhất 10 ký tự.',
            'status.in'        => 'Trạng thái không hợp lệ. Chỉ chấp nhận draft, published, archived.',
        ];
    }
}
