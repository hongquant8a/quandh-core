<?php

namespace App\Modules\Core;

use App\Http\Controllers\Controller;
use App\Modules\Core\Requests\FilterRequest;
use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Requests\StoreOrganizationRequest;
use App\Modules\Core\Requests\UpdateOrganizationRequest;
use App\Modules\Core\Requests\BulkDestroyOrganizationRequest;
use App\Modules\Core\Requests\BulkUpdateStatusOrganizationRequest;
use App\Modules\Core\Requests\ChangeStatusOrganizationRequest;
use App\Modules\Core\Requests\ImportOrganizationRequest;
use App\Modules\Core\Resources\OrganizationResource;
use App\Modules\Core\Resources\OrganizationCollection;
use App\Modules\Core\Resources\OrganizationTreeResource;
use Illuminate\Http\Request;
use App\Modules\Core\Exports\OrganizationsExport;
use App\Modules\Core\Imports\OrganizationsImport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @group Core - Organization
 *
 * Quản lý tổ chức (organization): stats, index, show, store, update, destroy, bulk delete, bulk status, change status, export, import.
 */
class OrganizationController extends Controller
{
    /**
     * Thống kê organization
     *
     * Tổng số, đang kích hoạt (active), không kích hoạt (inactive). Áp dụng cùng bộ lọc với index.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, slug). Example: cong-ty
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d). Example: 2026-02-01
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d). Example: 2026-02-17
     * @queryParam sort_by string Sắp xếp theo: id, name, slug, status, created_at, updated_at. Example: created_at
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function stats(FilterRequest $request)
    {
        $base = Organization::filter($request->all());
        return $this->success([
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('status', StatusEnum::Active->value)->count(),
            'inactive' => (clone $base)->where('status', '!=', StatusEnum::Active->value)->count(),
        ]);
    }

    /**
     * Danh sách organization
     *
     * Lấy danh sách có phân trang, lọc và sắp xếp.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, slug). Example: cong-ty
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d). Example: 2026-02-01
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d). Example: 2026-02-17
     * @queryParam sort_by string Sắp xếp theo: id, name, slug, status, created_at, updated_at. Example: id
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function index(FilterRequest $request)
    {
        $items = Organization::with(['creator', 'editor', 'parent'])
            ->filter($request->all())
            ->treeOrder()
            ->paginate($request->limit ?? 10);
        return $this->successCollection(new OrganizationCollection($items));
    }

    /**
     * Cây organization (toàn bộ cây, không phân trang). Cấu trúc parent_id.
     *
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     */
    public function tree(Request $request)
    {
        $query = Organization::query()
            ->when($request->status, fn ($q, $v) => $q->where('status', $v));
        $items = $query->orderBy('sort_order')->orderBy('id')->get();
        $tree = Organization::buildTree($items);
        return $this->successCollection(OrganizationTreeResource::collection($tree));
    }

    /**
     * Chi tiết organization
     *
     * @urlParam organization integer required ID organization. Example: 1
     */
    public function show(Organization $organization)
    {
        $organization->load(['creator', 'editor', 'parent', 'children' => fn ($q) => $q->orderBy('sort_order')]);
        return $this->successResource(new OrganizationResource($organization));
    }

    /**
     * Tạo organization mới
     *
     * @bodyParam name string required Tên organization. Example: Công ty A
     * @bodyParam slug string Slug (nếu không gửi sẽ tự sinh từ name). Example: cong-ty-a
     * @bodyParam description string Mô tả. Example: Tổ chức quản trị
     * @bodyParam status string required Trạng thái: active, inactive. Example: active
     * @bodyParam parent_id integer ID organization cha (null = gốc). Example: null
     * @bodyParam sort_order integer Thứ tự. Example: 0
     */
    public function store(StoreOrganizationRequest $request)
    {
        $organization = Organization::create($request->validated());
        return $this->successResource(new OrganizationResource($organization), 'Organization đã được tạo thành công!', 201);
    }

    /**
     * Cập nhật organization
     *
     * @urlParam organization integer required ID organization. Example: 1
     * @bodyParam name string Tên organization. Example: Công ty A
     * @bodyParam slug string Slug. Example: cong-ty-a
     * @bodyParam description string Mô tả. Example: Tổ chức quản trị
     * @bodyParam status string Trạng thái: active, inactive. Example: inactive
     * @bodyParam parent_id integer ID organization cha (null = gốc). Example: null
     * @bodyParam sort_order integer Thứ tự. Example: 0
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        $data = $request->validated();
        if (isset($data['parent_id']) && (int) $data['parent_id'] !== 0) {
            if (static::isDescendantOf($data['parent_id'], $organization->id)) {
                return $this->error('Không thể chọn organization con làm organization cha.', 422, null, 'CONFLICT');
            }
        }
        if (array_key_exists('parent_id', $data) && (int) $data['parent_id'] === 0) {
            $data['parent_id'] = null;
        }
        $organization->update($data);
        return $this->successResource(new OrganizationResource($organization->fresh(['parent', 'children'])), 'Organization đã được cập nhật!');
    }

    protected static function isDescendantOf(int $candidateId, int $id): bool
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

    /**
     * Xóa organization
     *
     * @urlParam organization integer required ID organization. Example: 1
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();
        return $this->success(null, 'Organization đã được xóa!');
    }

    /**
     * Xóa hàng loạt organization
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     */
    public function bulkDestroy(BulkDestroyOrganizationRequest $request)
    {
        Organization::whereIn('id', $request->ids)->delete();
        return $this->success(null, 'Đã xóa thành công các organization được chọn!');
    }

    /**
     * Cập nhật trạng thái organization hàng loạt
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     * @bodyParam status string required Trạng thái: active, inactive. Example: active
     */
    public function bulkUpdateStatus(BulkUpdateStatusOrganizationRequest $request)
    {
        Organization::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return $this->success(null, 'Cập nhật trạng thái organization thành công.');
    }

    /**
     * Thay đổi trạng thái organization
     *
     * @urlParam organization integer required ID organization. Example: 1
     * @bodyParam status string required Trạng thái mới: active, inactive. Example: inactive
     */
    public function changeStatus(ChangeStatusOrganizationRequest $request, Organization $organization)
    {
        $organization->update(['status' => $request->status]);
        return $this->successResource(new OrganizationResource($organization->load(['parent', 'children'])), 'Cập nhật trạng thái thành công!');
    }

    /**
     * Xuất danh sách organization
     *
     * Áp dụng cùng bộ lọc với index. Trả về file Excel.
     *
     * @queryParam search string Từ khóa tìm kiếm (name, slug).
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d).
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d).
     * @queryParam sort_by string Sắp xếp theo: id, name, slug, status, created_at, updated_at.
     * @queryParam sort_order string Thứ tự: asc, desc.
     */
    public function export(FilterRequest $request)
    {
        return Excel::download(new OrganizationsExport($request->all()), 'organizations.xlsx');
    }

    /**
     * Nhập danh sách organization
     *
     * @bodyParam file file required File excel (xlsx, xls, csv). Cột: name, slug, description, status.
     */
    public function import(ImportOrganizationRequest $request)
    {
        Excel::import(new OrganizationsImport, $request->file('file'));
        return $this->success(null, 'Import organization thành công.');
    }
}
