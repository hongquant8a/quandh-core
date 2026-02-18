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
        $team = $this->route('team');
        $teamId = is_object($team) ? $team->id : $team;
        return [
            'name'        => 'sometimes|string|max:255',
            'slug'        => ['nullable', 'string', 'max:255', Rule::unique('teams', 'slug')->ignore($teamId)],
            'description' => 'nullable|string',
            'status'      => 'nullable|in:active,inactive',
            'parent_id'   => [
                'nullable',
                Rule::notIn([$teamId]),
                Rule::when($this->filled('parent_id') && (int) $this->input('parent_id') !== 0, ['exists:teams,id']),
            ],
            'sort_order'  => 'nullable|integer|min:0',
        ];
    }
}
