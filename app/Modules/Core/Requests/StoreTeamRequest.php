<?php

namespace App\Modules\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:teams,slug',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên team không được để trống.',
            'slug.unique'   => 'Slug team đã tồn tại.',
            'status.in'     => 'Trạng thái chỉ chấp nhận active, inactive.',
        ];
    }
}
