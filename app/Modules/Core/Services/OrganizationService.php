<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Exports\OrganizationsExport;
use App\Modules\Core\Imports\OrganizationsImport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrganizationService
{
    public function stats(array $filters): array
    {
        $base = Organization::filter($filters);

        return [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('status', StatusEnum::Active->value)->count(),
            'inactive' => (clone $base)->where('status', '!=', StatusEnum::Active->value)->count(),
        ];
    }

    public function index(array $filters, int $limit)
    {
        return Organization::with(['creator', 'editor', 'parent'])
            ->filter($filters)
            ->treeOrder()
            ->paginate($limit);
    }

    public function tree(?string $status)
    {
        $query = Organization::query()
            ->when($status, fn ($q, $value) => $q->where('status', $value));
        $items = $query->orderBy('sort_order')->orderBy('id')->get();

        return Organization::buildTree($items);
    }

    public function show(Organization $organization): Organization
    {
        return $organization->load(['creator', 'editor', 'parent', 'children' => fn ($q) => $q->orderBy('sort_order')]);
    }

    public function store(array $data): Organization
    {
        return Organization::create($data);
    }

    public function update(Organization $organization, array $data): array
    {
        if (isset($data['parent_id']) && (int) $data['parent_id'] !== 0) {
            if ($this->isDescendantOf((int) $data['parent_id'], $organization->id)) {
                return [
                    'ok' => false,
                    'message' => 'Không thể chọn organization con làm organization cha.',
                    'code' => 422,
                    'error_code' => 'CONFLICT',
                ];
            }
        }

        if (array_key_exists('parent_id', $data) && (int) $data['parent_id'] === 0) {
            $data['parent_id'] = null;
        }

        $organization->update($data);

        return [
            'ok' => true,
            'organization' => $organization->fresh(['parent', 'children']),
        ];
    }

    public function destroy(Organization $organization): void
    {
        $organization->delete();
    }

    public function bulkDestroy(array $ids): void
    {
        Organization::whereIn('id', $ids)->delete();
    }

    public function bulkUpdateStatus(array $ids, string $status): void
    {
        Organization::whereIn('id', $ids)->update(['status' => $status]);
    }

    public function changeStatus(Organization $organization, string $status): Organization
    {
        $organization->update(['status' => $status]);

        return $organization->load(['parent', 'children']);
    }

    public function export(array $filters): BinaryFileResponse
    {
        return Excel::download(new OrganizationsExport($filters), 'organizations.xlsx');
    }

    public function import($file): void
    {
        Excel::import(new OrganizationsImport(), $file);
    }

    private function isDescendantOf(int $candidateId, int $id): bool
    {
        $current = Organization::find($id);

        while ($current && $current->parent_id) {
            if ($current->parent_id === $candidateId) {
                return true;
            }

            $current = Organization::find($current->parent_id);
        }

        return false;
    }
}
