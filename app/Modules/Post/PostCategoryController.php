<?php

namespace App\Modules\Post;

use App\Http\Controllers\Controller;
use App\Modules\Core\Requests\FilterRequest;
use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Post\Models\PostCategory;
use App\Modules\Post\Requests\StorePostCategoryRequest;
use App\Modules\Post\Requests\UpdatePostCategoryRequest;
use App\Modules\Post\Requests\BulkDestroyPostCategoryRequest;
use App\Modules\Post\Requests\BulkUpdateStatusPostCategoryRequest;
use App\Modules\Post\Requests\ImportPostCategoryRequest;
use App\Modules\Post\Requests\ChangeStatusPostCategoryRequest;
use App\Modules\Post\Resources\PostCategoryResource;
use App\Modules\Post\Resources\PostCategoryCollection;
use App\Modules\Post\Resources\PostCategoryTreeResource;
use App\Modules\Post\Exports\PostCategoriesExport;
use App\Modules\Post\Imports\PostCategoriesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @group Post Category
 *
 * Quản lý danh mục tin tức phân cấp (cấu trúc cây parent_id): danh sách, cây, chi tiết, tạo, cập nhật, xóa, thao tác hàng loạt, xuất/nhập, đổi trạng thái.
 */
class PostCategoryController extends Controller
{
    /**
     * Thống kê danh mục tin tức
     *
     * Tổng số, đang kích hoạt (active), không kích hoạt (inactive). Áp dụng cùng bộ lọc với index.
     *
     * @queryParam search string Từ khóa tìm kiếm (name). Example: tin-tuc
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d). Example: 2026-02-01
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d). Example: 2026-02-17
     * @queryParam sort_by string Sắp xếp theo: id, name, sort_order, parent_id, created_at. Example: sort_order
     * @queryParam sort_order string Thứ tự: asc, desc. Example: asc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     * @response 200 {"success": true, "data": {"total": 10, "active": 5, "inactive": 5}}
     */
    public function stats(FilterRequest $request)
    {
        $base = PostCategory::filter($request->all());
        return $this->success([
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('status', StatusEnum::Active->value)->count(),
            'inactive' => (clone $base)->where('status', '!=', StatusEnum::Active->value)->count(),
        ]);
    }

    /**
     * Danh sách danh mục (dạng phẳng, phân trang, thứ tự cây)
     *
     * @queryParam search string Từ khóa tìm kiếm (name). Example: tin-tuc
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     * @queryParam from_date date Lọc từ ngày tạo (created_at) (Y-m-d). Example: 2026-02-01
     * @queryParam to_date date Lọc đến ngày tạo (created_at) (Y-m-d). Example: 2026-02-17
     * @queryParam sort_by string Sắp xếp theo: id, name, sort_order, parent_id, created_at. Example: sort_order
     * @queryParam sort_order string Thứ tự: asc, desc. Example: asc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     * @apiResourceCollection App\Modules\Post\Resources\PostCategoryCollection
     * @apiResourceModel App\Modules\Post\Models\PostCategory paginate=10
     * @apiResourceAdditional success=true
     */
    public function index(FilterRequest $request)
    {
        $categories = PostCategory::with(['creator', 'editor', 'parent'])
            ->filter($request->all())
            ->treeOrder()
            ->paginate($request->limit ?? 10);
        return $this->successCollection(new PostCategoryCollection($categories));
    }

    /**
     * Cây danh mục (toàn bộ cây, không phân trang). Cấu trúc parent_id, children đệ quy.
     *
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     * @response 200 {"success": true, "data": [{"id": 1, "name": "Tin tức", "slug": "tin-tuc", "status": "active", "sort_order": 0, "parent_id": null, "depth": 0, "children": []}]}
     */
    public function tree(Request $request)
    {
        $query = PostCategory::query()
            ->when($request->status, fn ($q, $v) => $q->where('status', $v));
        $items = $query->orderBy('sort_order')->orderBy('id')->get();
        $tree = PostCategory::buildTree($items);
        return $this->successCollection(PostCategoryTreeResource::collection($tree));
    }

    /**
     * Chi tiết danh mục
     *
     * @urlParam category integer required ID danh mục. Example: 1
     * @apiResource App\Modules\Post\Resources\PostCategoryResource
     * @apiResourceModel App\Modules\Post\Models\PostCategory with=parent,children
     * @apiResourceAdditional success=true
     */
    public function show(PostCategory $category)
    {
        $category->load(['creator', 'editor', 'parent', 'children' => fn ($q) => $q->orderBy('sort_order')]);
        return $this->successResource(new PostCategoryResource($category));
    }

    /**
     * Tạo danh mục mới
     *
     * @bodyParam name string required Tên danh mục. Example: Tin tức
     * @bodyParam slug string Slug (tự sinh từ name nếu không gửi). Example: tin-tuc
     * @bodyParam description string Mô tả.
     * @bodyParam status string required Trạng thái: active, inactive. Example: active
     * @bodyParam parent_id integer ID danh mục cha (null = gốc). Example: null
     * @bodyParam sort_order integer Thứ tự. Example: 0
     * @apiResource App\Modules\Post\Resources\PostCategoryResource status=201
     * @apiResourceModel App\Modules\Post\Models\PostCategory
     * @apiResourceAdditional success=true message="Danh mục đã được tạo thành công!"
     */
    public function store(StorePostCategoryRequest $request)
    {
        $data = $request->validated();
        $parentId = $data['parent_id'] ?? null;
        if ($parentId) {
            PostCategory::findOrFail($parentId);
        }
        $category = PostCategory::create($data);
        return $this->successResource(new PostCategoryResource($category), 'Danh mục đã được tạo thành công!', 201);
    }

    /**
     * Cập nhật danh mục
     *
     * @urlParam category integer required ID danh mục. Example: 1
     * @bodyParam name string Tên danh mục.
     * @bodyParam slug string Slug.
     * @bodyParam description string Mô tả.
     * @bodyParam status string Trạng thái: active, inactive.
     * @bodyParam parent_id integer ID danh mục cha (null hoặc 0 = gốc).
     * @bodyParam sort_order integer Thứ tự.
     * @apiResource App\Modules\Post\Resources\PostCategoryResource
     * @apiResourceModel App\Modules\Post\Models\PostCategory with=parent,children
     * @apiResourceAdditional success=true message="Danh mục đã được cập nhật!"
     */
    public function update(UpdatePostCategoryRequest $request, PostCategory $category)
    {
        $data = $request->validated();
        $parentId = array_key_exists('parent_id', $data) ? $data['parent_id'] : null;
        if ($parentId !== null && (int) $parentId !== 0) {
            $parent = PostCategory::findOrFail($parentId);
            if (static::isDescendantOf($parent->id, $category->id)) {
                return $this->error('Không thể chọn danh mục con làm danh mục cha.', 422, null, 'CONFLICT');
            }
        }
        if ($parentId !== null && (int) $parentId === 0) {
            $data['parent_id'] = null;
        }
        $category->update($data);
        return $this->successResource(new PostCategoryResource($category->fresh(['parent', 'children'])), 'Danh mục đã được cập nhật!');
    }

    /** Kiểm tra id có phải hậu duệ của candidateId không (tránh vòng). */
    protected static function isDescendantOf(int $candidateId, int $id): bool
    {
        $current = PostCategory::find($id);
        while ($current && $current->parent_id) {
            if ($current->parent_id === $candidateId) {
                return true;
            }
            $current = PostCategory::find($current->parent_id);
        }
        return false;
    }

    /**
     * Xóa danh mục
     *
     * @urlParam category integer required ID danh mục. Example: 1
     * @response 200 {"success": true, "message": "Danh mục đã được xóa!"}
     */
    public function destroy(PostCategory $category)
    {
        $category->delete();
        return $this->success(null, 'Danh mục đã được xóa!');
    }

    /**
     * Xóa hàng loạt danh mục
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     * @response 200 {"success": true, "message": "Đã xóa thành công các danh mục được chọn!"}
     */
    public function bulkDestroy(BulkDestroyPostCategoryRequest $request)
    {
        PostCategory::whereIn('id', $request->ids)->get()->each->delete();
        return $this->success(null, 'Đã xóa thành công các danh mục được chọn!');
    }

    /**
     * Cập nhật trạng thái danh mục hàng loạt
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     * @bodyParam status string required Trạng thái: active, inactive. Example: active
     * @response 200 {"success": true, "message": "Cập nhật trạng thái thành công các danh mục được chọn!"}
     */
    public function bulkUpdateStatus(BulkUpdateStatusPostCategoryRequest $request)
    {
        PostCategory::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return $this->success(null, 'Cập nhật trạng thái thành công các danh mục được chọn!');
    }

    /**
     * Xuất danh sách danh mục
     *
     * Áp dụng cùng bộ lọc với index. Trả về file Excel.
     *
     * @queryParam search string Từ khóa tìm kiếm (name).
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     * @queryParam from_date date Lọc từ ngày tạo (Y-m-d).
     * @queryParam to_date date Lọc đến ngày tạo (Y-m-d).
     * @queryParam sort_by string Sắp xếp theo: id, name, sort_order, parent_id, created_at.
     * @queryParam sort_order string Thứ tự: asc, desc.
     */
    public function export(FilterRequest $request)
    {
        return Excel::download(new PostCategoriesExport($request->all()), 'post-categories.xlsx');
    }

    /**
     * Nhập danh sách danh mục
     *
     * @bodyParam file file required File Excel (xlsx, xls, csv). Cột: name, slug, description, status, sort_order, parent_slug.
     * @response 200 {"success": true, "message": "Import danh mục bài viết thành công."}
     */
    public function import(ImportPostCategoryRequest $request)
    {
        Excel::import(new PostCategoriesImport, $request->file('file'));
        return $this->success(null, 'Import danh mục bài viết thành công.');
    }

    /**
     * Thay đổi trạng thái danh mục
     *
     * @urlParam category integer required ID danh mục. Example: 1
     * @bodyParam status string required Trạng thái mới: active, inactive. Example: active
     * @apiResource App\Modules\Post\Resources\PostCategoryResource
     * @apiResourceModel App\Modules\Post\Models\PostCategory with=parent,children
     * @apiResourceAdditional success=true message="Cập nhật trạng thái thành công!"
     */
    public function changeStatus(ChangeStatusPostCategoryRequest $request, PostCategory $category)
    {
        $category->update(['status' => $request->status]);
        return $this->successResource(new PostCategoryResource($category->load(['parent', 'children'])), 'Cập nhật trạng thái thành công!');
    }
}
