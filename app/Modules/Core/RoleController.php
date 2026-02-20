<?php

namespace App\Modules\Core;

use App\Http\Controllers\Controller;
use App\Modules\Core\Requests\FilterRequest;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Requests\StoreRoleRequest;
use App\Modules\Core\Requests\UpdateRoleRequest;
use App\Modules\Core\Requests\BulkDestroyRoleRequest;
use App\Modules\Core\Requests\ImportRoleRequest;
use App\Modules\Core\Resources\RoleResource;
use App\Modules\Core\Resources\RoleCollection;
use App\Modules\Core\Exports\RolesExport;
use App\Modules\Core\Imports\RolesImport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @group Core - Role
 *
 * Quản lý vai trò (role) theo Spatie: stats, index, show, store, update, destroy, bulk delete, export, import.
 */
class RoleController extends Controller
{
    /**
     * Thống kê role
     *
     * Tổng số bản ghi (áp dụng cùng bộ lọc với index). Role không có cột status (chuẩn Spatie).
     *
     * @queryParam search string Từ khóa tìm kiếm (name, guard_name). Example: admin
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d). Example: 2026-02-01
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d). Example: 2026-02-17
     * @queryParam sort_by string Sắp xếp theo: id, name, guard_name, created_at, updated_at. Example: created_at
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     * @response 200 {"success": true, "data": {"total": 5}}
     */
    public function stats(FilterRequest $request)
    {
        $base = Role::with('organization')->filter($request->all());
        return $this->success(['total' => (clone $base)->count()]);
    }

    /**
     * Danh sách role
     *
     * Lấy danh sách có phân trang, lọc và sắp xếp. Có kèm organization và permissions.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, guard_name). Example: admin
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d). Example: 2026-02-01
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d). Example: 2026-02-17
     * @queryParam sort_by string Sắp xếp theo: id, name, guard_name, created_at, updated_at. Example: id
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     * @apiResourceCollection App\Modules\Core\Resources\RoleCollection
     * @apiResourceModel App\Modules\Core\Models\Role paginate=10
     * @apiResourceAdditional success=true
     */
    public function index(FilterRequest $request)
    {
        $items = Role::with(['organization', 'permissions'])
            ->filter($request->all())
            ->paginate($request->limit ?? 10);
        return $this->successCollection(new RoleCollection($items));
    }

    /**
     * Chi tiết role
     *
     * @urlParam role integer required ID role. Example: 1
     * @apiResource App\Modules\Core\Resources\RoleResource
     * @apiResourceModel App\Modules\Core\Models\Role with=organization,permissions
     * @apiResourceAdditional success=true
     */
    public function show(Role $role)
    {
        $role->load(['organization', 'permissions']);
        return $this->successResource(new RoleResource($role));
    }

    /**
     * Tạo role mới
     *
     * @bodyParam name string required Tên role. Example: admin
     * @bodyParam guard_name string Guard name (mặc định web). Example: web
     * @bodyParam organization_id integer ID organization (nullable). Example: 1
     * @bodyParam permission_ids array Danh sách ID permission để sync. Example: [1, 2, 3]
     * @apiResource App\Modules\Core\Resources\RoleResource status=201
     * @apiResourceModel App\Modules\Core\Models\Role with=permissions
     * @apiResourceAdditional success=true message="Vai trò đã được tạo thành công!"
     */
    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? null;
        unset($data['permission_ids']);
        $data['guard_name'] = $data['guard_name'] ?? config('auth.defaults.guard', 'web');
        $role = Role::create($data);
        if (!empty($permissionIds)) {
            $role->syncPermissions($permissionIds);
        }
        return $this->successResource(new RoleResource($role->load('permissions')), 'Vai trò đã được tạo thành công!', 201);
    }

    /**
     * Cập nhật role
     *
     * @urlParam role integer required ID role. Example: 1
     * @bodyParam name string Tên role. Example: editor
     * @bodyParam guard_name string Guard name. Example: web
     * @bodyParam organization_id integer ID organization (nullable). Example: 1
     * @bodyParam permission_ids array Danh sách ID permission để sync (gửi mảng rỗng để bỏ hết). Example: [1, 2]
     * @apiResource App\Modules\Core\Resources\RoleResource
     * @apiResourceModel App\Modules\Core\Models\Role with=permissions
     * @apiResourceAdditional success=true message="Vai trò đã được cập nhật!"
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? null;
        unset($data['permission_ids']);
        $role->update($data);
        if ($permissionIds !== null) {
            $role->syncPermissions($permissionIds);
        }
        return $this->successResource(new RoleResource($role->load('permissions')), 'Vai trò đã được cập nhật!');
    }

    /**
     * Xóa role
     *
     * @urlParam role integer required ID role. Example: 1
     * @response 200 {"success": true, "message": "Vai trò đã được xóa!"}
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return $this->success(null, 'Vai trò đã được xóa!');
    }

    /**
     * Xóa hàng loạt role
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     * @response 200 {"success": true, "message": "Đã xóa thành công các vai trò được chọn!"}
     */
    public function bulkDestroy(BulkDestroyRoleRequest $request)
    {
        Role::whereIn('id', $request->ids)->delete();
        return $this->success(null, 'Đã xóa thành công các vai trò được chọn!');
    }

    /**
     * Xuất danh sách role
     *
     * Áp dụng cùng bộ lọc với index. Trả về file Excel.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, guard_name).
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d).
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d).
     * @queryParam sort_by string Sắp xếp theo: id, name, guard_name, created_at, updated_at.
     * @queryParam sort_order string Thứ tự: asc, desc.
     */
    public function export(FilterRequest $request)
    {
        return Excel::download(new RolesExport($request->all()), 'roles.xlsx');
    }

    /**
     * Nhập danh sách role
     *
     * @bodyParam file file required File excel (xlsx, xls, csv). Cột: name, guard_name, organization_id.
     * @response 200 {"success": true, "message": "Import vai trò thành công."}
     */
    public function import(ImportRoleRequest $request)
    {
        Excel::import(new RolesImport, $request->file('file'));
        return $this->success(null, 'Import vai trò thành công.');
    }
}
