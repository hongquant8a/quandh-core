<?php

namespace App\Modules\Core\Imports;

use App\Modules\Core\Models\Permission;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PermissionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $guard = $row['guard_name'] ?? config('auth.defaults.guard', 'web');
        return new Permission([
            'name'       => $row['name'] ?? $row['name_'] ?? '',
            'guard_name' => $guard,
        ]);
    }
}
