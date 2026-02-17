<?php

namespace App\Modules\Core\Imports;

use App\Modules\Core\Models\Team;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeamsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $name = $row['name'] ?? $row['name_'] ?? '';
        $status = $row['status'] ?? 'active';
        return new Team([
            'name'        => $name,
            'slug'       => $row['slug'] ?? null,
            'description' => $row['description'] ?? null,
            'status'     => in_array($status, ['active', 'inactive']) ? $status : 'active',
        ]);
    }
}
