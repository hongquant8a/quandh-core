<?php

namespace App\Modules\Core;

use App\Http\Controllers\Controller;
use App\Modules\Core\Requests\FilterRequest;
use App\Modules\Core\Models\Permission;
use App\Modules\Core\Requests\StorePermissionRequest;
use App\Modules\Core\Requests\UpdatePermissionRequest;
use App\Modules\Core\Requests\BulkDestroyPermissionRequest;
use App\Modules\Core\Requests\ImportPermissionRequest;
use App\Modules\Core\Resources\PermissionResource;
use App\Modules\Core\Resources\PermissionCollection;
use App\Modules\Core\Resources\PermissionTreeResource;
use Illuminate\Http\Request;
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
    /**
     * Thống kê permission
     *
     * Tổng số bản ghi sau khi áp dụng bộ lọc.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, guard_name, description). Example: posts
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d). Example: 2026-02-01
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d). Example: 2026-02-17
     * @queryParam sort_by string Sắp xếp theo: id, name, guard_name, created_at, updated_at. Example: created_at
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function stats(FilterRequest $request)
    {
        $base = Permission::filter($request->all());
        return $this->success(['total' => (clone $base)->count()]);
    }

    /**
     * Danh sách permission
     *
     * Lấy danh sách có phân trang, lọc và sắp xếp.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, guard_name, description). Example: posts
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d). Example: 2026-02-01
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d). Example: 2026-02-17
     * @queryParam sort_by string Sắp xếp theo: id, name, guard_name, description, sort_order, parent_id, created_at, updated_at. Example: sort_order
     * @queryParam sort_order string Thứ tự: asc, desc. Example: asc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function index(FilterRequest $request)
    {
        $items = Permission::with('parent')
            ->filter($request->all())
            ->treeOrder()
            ->paginate($request->limit ?? 10);
        return $this->successCollection(new PermissionCollection($items));
    }

    /**
     * Cây permission (toàn bộ cây, không phân trang). Để hiển thị nhóm quyền trên frontend.
     *
     * @queryParam parent_id integer Lọc theo parent_id (null = gốc). Example: null
     */
    public function tree(Request $request)
    {
        $query = Permission::query()
            ->when($request->has('parent_id'), fn ($q) => $q->where('parent_id', $request->parent_id));
        $items = $query->orderBy('sort_order')->orderBy('id')->get();
        $tree = Permission::buildTree($items);
        return $this->successCollection(PermissionTreeResource::collection($tree));
    }

    /**
     * Chi tiết permission
     *
     * @urlParam permission integer required ID permission. Example: 1
     */
    public function show(Permission $permission)
    {
        $permission->load(['parent', 'children']);
        return $this->successResource(new PermissionResource($permission));
    }

    /**
     * Tạo permission mới
     *
     * @bodyParam name string required Tên permission. Example: posts.create
     * @bodyParam guard_name string Guard name (mặc định web). Example: web
     * @bodyParam description string Mô tả hiển thị trên frontend.
     * @bodyParam sort_order integer Thứ tự sắp xếp. Example: 0
     * @bodyParam parent_id integer ID permission cha (null = gốc/nhóm).
     */
    public function store(StorePermissionRequest $request)
    {
        $data = $request->validated();
        $data['guard_name'] = $data['guard_name'] ?? config('auth.defaults.guard', 'web');
        $permission = Permission::create($data);
        return $this->successResource(new PermissionResource($permission), 'Quyền đã được tạo thành công!', 201);
    }

    /**
     * Cập nhật permission
     *
     * @urlParam permission integer required ID permission. Example: 1
     * @bodyParam name string Tên permission. Example: posts.update
     * @bodyParam guard_name string Guard name. Example: web
     * @bodyParam description string Mô tả.
     * @bodyParam sort_order integer Thứ tự sắp xếp.
     * @bodyParam parent_id integer ID permission cha (null = gốc).
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $data = $request->validated();
        $permission->update($data);
        return $this->successResource(new PermissionResource($permission), 'Quyền đã được cập nhật!');
    }

    /**
     * Xóa permission
     *
     * @urlParam permission integer required ID permission. Example: 1
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return $this->success(null, 'Quyền đã được xóa!');
    }

    /**
     * Xóa hàng loạt permission
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     */
    public function bulkDestroy(BulkDestroyPermissionRequest $request)
    {
        Permission::whereIn('id', $request->ids)->delete();
        return $this->success(null, 'Đã xóa thành công các quyền được chọn!');
    }

    /**
     * Xuất danh sách permission
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
        return Excel::download(new PermissionsExport($request->all()), 'permissions.xlsx');
    }

    /**
     * Nhập danh sách permission
     *
     * @bodyParam file file required File excel (xlsx, xls, csv). Cột: name, guard_name, description, sort_order, parent_id.
     */
    public function import(ImportPermissionRequest $request)
    {
        Excel::import(new PermissionsImport, $request->file('file'));
        return $this->success(null, 'Import quyền thành công.');
    }
}
