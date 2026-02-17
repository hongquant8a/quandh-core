<?php

namespace App\Modules\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'sometimes|string|max:255',
            'guard_name'    => 'nullable|string|max:255',
            'team_id'       => 'nullable|exists:teams,id',
            'status'        => 'nullable|in:active,inactive',
            'permission_ids'   => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ];
    }
}
