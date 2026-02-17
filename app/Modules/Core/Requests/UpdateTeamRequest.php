<?php

namespace App\Modules\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teamId = $this->route('team')?->id;
        return [
            'name'        => 'sometimes|string|max:255',
            'slug'        => ['nullable', 'string', 'max:255', Rule::unique('teams', 'slug')->ignore($teamId)],
            'description' => 'nullable|string',
            'status'      => 'nullable|in:active,inactive',
        ];
    }
}
