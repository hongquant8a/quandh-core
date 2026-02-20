<?php

namespace App\Modules\Core\Imports;

use App\Modules\Core\Models\Organization;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrganizationsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $parentSlug = $row['parent_slug'] ?? $row['parent slug'] ?? '';
        $parent = $parentSlug ? Organization::where('slug', $parentSlug)->first() : null;
        $name = $row['name'] ?? $row['name_'] ?? '';
        $status = $row['status'] ?? 'active';
        return new Organization([
            'name'        => $name,
            'slug'       => $row['slug'] ?? null,
            'description' => $row['description'] ?? null,
            'status'     => in_array($status, ['active', 'inactive']) ? $status : 'active',
            'parent_id'  => $parent?->id,
            'sort_order' => (int) ($row['sort_order'] ?? 0),
        ]);
    }
}
