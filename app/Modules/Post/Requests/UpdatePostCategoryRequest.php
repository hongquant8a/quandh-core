<?php

namespace App\Modules\Post\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category');
        return [
            'name'        => 'sometimes|string|max:255',
            'slug'        => 'sometimes|string|max:255|unique:post_categories,slug,' . $categoryId,
            'description' => 'nullable|string|max:65535',
            'status'      => 'sometimes|in:active,inactive',
            'sort_order'  => 'nullable|integer|min:0',
            'parent_id'   => 'nullable|exists:post_categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'Slug danh mục đã tồn tại.',
            'parent_id.exists' => 'Danh mục cha không tồn tại.',
        ];
    }
}
