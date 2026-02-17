<?php

namespace App\Modules\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Requests\StoreRoleRequest;
use App\Modules\Core\Requests\UpdateRoleRequest;
use App\Modules\Core\Requests\BulkDestroyRoleRequest;
use App\Modules\Core\Requests\BulkUpdateStatusRoleRequest;
use App\Modules\Core\Requests\ChangeStatusRoleRequest;
use App\Modules\Core\Requests\ImportRoleRequest;
use App\Modules\Core\Resources\RoleResource;
use App\Modules\Core\Resources\RoleCollection;
use App\Modules\Core\Exports\RolesExport;
use App\Modules\Core\Imports\RolesImport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @group Core - Role
 *
 * Quản lý vai trò (role): stats, index, show, store, update, destroy, bulk delete, bulk status, change status, export, import.
 */
class RoleController extends Controller
{
    public function stats(FilterRequest $request)
    {
        $base = Role::with('team')->filter($request->all());
        return response()->json([
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('status', 'active')->count(),
            'inactive' => (clone $base)->where('status', '!=', 'active')->count(),
        ]);
    }

    public function index(FilterRequest $request)
    {
        $items = Role::with(['team', 'permissions'])
            ->filter($request->all())
            ->paginate($request->limit ?? 10);
        return new RoleCollection($items);
    }

    public function show(Role $role)
    {
        $role->load(['team', 'permissions']);
        return new RoleResource($role);
    }

    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? null;
        unset($data['permission_ids']);
        $data['guard_name'] = $data['guard_name'] ?? config('auth.defaults.guard', 'web');
        $data['status'] = $data['status'] ?? 'active';
        $role = Role::create($data);
        if (!empty($permissionIds)) {
            $role->syncPermissions($permissionIds);
        }
        return (new RoleResource($role->load('permissions')))
            ->additional(['message' => 'Vai trò đã được tạo thành công!']);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? null;
        unset($data['permission_ids']);
        $role->update($data);
        if ($permissionIds !== null) {
            $role->syncPermissions($permissionIds);
        }
        return (new RoleResource($role->load('permissions')))
            ->additional(['message' => 'Vai trò đã được cập nhật!']);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Vai trò đã được xóa!']);
    }

    public function bulkDestroy(BulkDestroyRoleRequest $request)
    {
        Role::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'Đã xóa thành công các vai trò được chọn!']);
    }

    public function bulkUpdateStatus(BulkUpdateStatusRoleRequest $request)
    {
        Role::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return response()->json(['message' => 'Cập nhật trạng thái vai trò thành công.']);
    }

    public function changeStatus(ChangeStatusRoleRequest $request, Role $role)
    {
        $role->update(['status' => $request->status]);
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'data'    => new RoleResource($role),
        ]);
    }

    public function export(FilterRequest $request)
    {
        return Excel::download(new RolesExport($request->all()), 'roles.xlsx');
    }

    public function import(ImportRoleRequest $request)
    {
        Excel::import(new RolesImport, $request->file('file'));
        return response()->json(['message' => 'Import vai trò thành công.']);
    }
}
