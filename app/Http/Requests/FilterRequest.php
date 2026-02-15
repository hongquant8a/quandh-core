<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'     => 'nullable|string|max:100',
            'status'     => 'nullable|string',
            'sort_by'    => 'nullable|in:id,title,name,created_at',
            'sort_order' => 'nullable|in:asc,desc',
            'limit'      => 'nullable|integer|min:1|max:100',
        ];                  
    }

    public function messages(): array
    {
        return [
            'search.string' => 'Từ khóa tìm kiếm phải là một chuỗi ký tự.',
            'search.max' => 'Từ khóa tìm kiếm không được vượt quá 100 ký tự.',
            'status.string' => 'Trạng thái phải là một chuỗi ký tự.',
            'sort_by.in' => 'Trường sắp xếp không hợp lệ.',
            'sort_order.in' => 'Thứ tự sắp xếp không hợp lệ.',
            'limit.integer' => 'Số lượng phải là một số nguyên.',
            'limit.min' => 'Số lượng phải lớn hơn 0.',
            'limit.max' => 'Số lượng phải nhỏ hơn 100.',
        ];
    }
}
