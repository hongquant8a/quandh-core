<?php

namespace App\Modules\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids'   => 'required|array|min:1',
            'ids.*' => 'exists:teams,id',
        ];
    }
}
