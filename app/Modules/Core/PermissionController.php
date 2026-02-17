<?php

namespace App\Modules\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Modules\Core\Models\Permission;
use App\Modules\Core\Requests\StorePermissionRequest;
use App\Modules\Core\Requests\UpdatePermissionRequest;
use App\Modules\Core\Requests\BulkDestroyPermissionRequest;
use App\Modules\Core\Requests\ImportPermissionRequest;
use App\Modules\Core\Resources\PermissionResource;
use App\Modules\Core\Resources\PermissionCollection;
use App\Modules\Core\Exports\PermissionsExport;
use App\Modules\Core\Imports\PermissionsImport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @group Core - Permission
 *
 * Quản lý quyền (permission): stats, index, show, store, update, destroy, bulk delete, export, import.
 */
class PermissionController extends Controller
{
    public function stats(FilterRequest $request)
    {
        $base = Permission::filter($request->all());
        return response()->json([
            'total' => (clone $base)->count(),
        ]);
    }

    public function index(FilterRequest $request)
    {
        $items = Permission::filter($request->all())
            ->paginate($request->limit ?? 10);
        return new PermissionCollection($items);
    }

    public function show(Permission $permission)
    {
        return new PermissionResource($permission);
    }

    public function store(StorePermissionRequest $request)
    {
        $data = $request->validated();
        $data['guard_name'] = $data['guard_name'] ?? config('auth.defaults.guard', 'web');
        $permission = Permission::create($data);
        return (new PermissionResource($permission))
            ->additional(['message' => 'Quyền đã được tạo thành công!']);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $data = $request->validated();
        if (array_key_exists('guard_name', $data)) {
            $permission->guard_name = $data['guard_name'];
        }
        if (array_key_exists('name', $data)) {
            $permission->name = $data['name'];
        }
        $permission->save();
        return (new PermissionResource($permission))
            ->additional(['message' => 'Quyền đã được cập nhật!']);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['message' => 'Quyền đã được xóa!']);
    }

    public function bulkDestroy(BulkDestroyPermissionRequest $request)
    {
        Permission::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'Đã xóa thành công các quyền được chọn!']);
    }

    public function export(FilterRequest $request)
    {
        return Excel::download(new PermissionsExport($request->all()), 'permissions.xlsx');
    }

    public function import(ImportPermissionRequest $request)
    {
        Excel::import(new PermissionsImport, $request->file('file'));
        return response()->json(['message' => 'Import quyền thành công.']);
    }
}
