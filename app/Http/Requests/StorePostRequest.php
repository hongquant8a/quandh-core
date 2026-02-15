<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'title'   => 'required|string|max:255|unique:posts,title',
            'content' => 'required|string|min:10',
            'status'  => 'required|in:draft,published,archived',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',    
            'title.string'   => 'Tiêu đề phải là một chuỗi ký tự.', 
            'title.max'      => 'Tiêu đề không được vượt quá 255 ký tự.',
            'title.unique'   => 'Tiêu đề bài viết đã tồn tại.',
            'content.required' => 'Nội dung không được để trống.',
            'content.string'   => 'Nội dung phải là một chuỗi ký tự.',
            'content.min'      => 'Nội dung phải có ít nhất 10 ký tự.',
            'status.in'        => 'Trạng thái không hợp lệ. Chỉ chấp nhận draft, published, archived.',
        ];
    }
}
