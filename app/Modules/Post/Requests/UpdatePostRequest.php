<?php

namespace App\Modules\Post\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                 => 'sometimes|string|max:255|unique:posts,title,' . $this->route('post'),
            'content'               => 'sometimes|string|min:10',
            'status'                => 'sometimes|in:draft,published,archived',
            'category_id'            => 'nullable|exists:post_categories,id',
            'images'                => 'nullable|array|max:10',
            'images.*'              => 'image|mimes:jpeg,png,gif,webp|max:5120',
            'remove_attachment_ids' => 'nullable|array',
            'remove_attachment_ids.*' => 'integer|exists:post_attachments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string'   => 'Tiêu đề phải là một chuỗi ký tự.',
            'title.max'      => 'Tiêu đề không được vượt quá 255 ký tự.',
            'title.unique'   => 'Tiêu đề bài viết đã tồn tại.',
            'content.string' => 'Nội dung phải là một chuỗi ký tự.',
            'content.min'    => 'Nội dung phải có ít nhất 10 ký tự.',
            'status.in'      => 'Trạng thái không hợp lệ. Chỉ chấp nhận draft, published, archived.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'images.*.image'     => 'File phải là hình ảnh.',
        ];
    }
}
