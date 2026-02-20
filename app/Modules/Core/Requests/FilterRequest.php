<?php

namespace App\Modules\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request chuẩn cho bộ lọc index/stats/export: search, status, from_date, to_date, sort_by, sort_order, limit.
 */
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
            'from_date'  => 'nullable|date',
            'to_date'    => 'nullable|date|after_or_equal:from_date',
            'sort_by'    => 'nullable|string|max:50',
            'sort_order' => 'nullable|in:asc,desc',
            'limit'      => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'search.string'    => 'Từ khóa tìm kiếm phải là một chuỗi ký tự.',
            'search.max'       => 'Từ khóa tìm kiếm không được vượt quá 100 ký tự.',
            'status.string'    => 'Trạng thái phải là một chuỗi ký tự.',
            'sort_by.in'       => 'Trường sắp xếp không hợp lệ.',
            'sort_order.in'    => 'Thứ tự sắp xếp không hợp lệ.',
            'limit.integer'    => 'Số lượng phải là một số nguyên.',
            'limit.min'        => 'Số lượng phải lớn hơn 0.',
            'limit.max'        => 'Số lượng phải nhỏ hơn 100.',
        ];
    }
}
