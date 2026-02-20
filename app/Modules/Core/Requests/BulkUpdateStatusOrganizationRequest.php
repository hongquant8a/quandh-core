<?php

namespace App\Modules\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateStatusOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'exists:organizations,id',
            'status' => 'required|in:active,inactive',
        ];
    }
}
