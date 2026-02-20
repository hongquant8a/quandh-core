<?php

namespace App\Modules\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
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
     */
    public function stats(FilterRequest $request)
    {
        $base = PostCategory::filter($request->all());
        return response()->json([
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
     */
    public function index(FilterRequest $request)
    {
        $categories = PostCategory::with(['creator', 'editor', 'parent'])
            ->filter($request->all())
            ->treeOrder()
            ->paginate($request->limit ?? 10);
        return new PostCategoryCollection($categories);
    }

    /**
     * Cây danh mục (toàn bộ cây, không phân trang). Cấu trúc parent_id, children đệ quy.
     *
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     */
    public function tree(Request $request)
    {
        $query = PostCategory::query()
            ->when($request->status, fn ($q, $v) => $q->where('status', $v));
        $items = $query->orderBy('sort_order')->orderBy('id')->get();
        $tree = PostCategory::buildTree($items);
        return PostCategoryTreeResource::collection($tree);
    }

    /**
     * Chi tiết danh mục
     *
     * @urlParam category integer required ID danh mục. Example: 1
     */
    public function show(PostCategory $category)
    {
        $category->load(['creator', 'editor', 'parent', 'children' => fn ($q) => $q->orderBy('sort_order')]);
        return new PostCategoryResource($category);
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
     */
    public function store(StorePostCategoryRequest $request)
    {
        $data = $request->validated();
        $parentId = $data['parent_id'] ?? null;
        if ($parentId) {
            PostCategory::findOrFail($parentId);
        }
        $category = PostCategory::create($data);
        return (new PostCategoryResource($category))
            ->additional(['message' => 'Danh mục đã được tạo thành công!']);
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
     */
    public function update(UpdatePostCategoryRequest $request, PostCategory $category)
    {
        $data = $request->validated();
        $parentId = array_key_exists('parent_id', $data) ? $data['parent_id'] : null;
        if ($parentId !== null && (int) $parentId !== 0) {
            $parent = PostCategory::findOrFail($parentId);
            if (static::isDescendantOf($parent->id, $category->id)) {
                return response()->json(['message' => 'Không thể chọn danh mục con làm danh mục cha.'], 422);
            }
        }
        if ($parentId !== null && (int) $parentId === 0) {
            $data['parent_id'] = null;
        }
        $category->update($data);
        return (new PostCategoryResource($category->fresh(['parent', 'children'])))
            ->additional(['message' => 'Danh mục đã được cập nhật!']);
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
     */
    public function destroy(PostCategory $category)
    {
        $category->delete();
        return response()->json(['message' => 'Danh mục đã được xóa!']);
    }

    /**
     * Xóa hàng loạt danh mục
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     */
    public function bulkDestroy(BulkDestroyPostCategoryRequest $request)
    {
        PostCategory::whereIn('id', $request->ids)->get()->each->delete();
        return response()->json(['message' => 'Đã xóa thành công các danh mục được chọn!']);
    }

    /**
     * Cập nhật trạng thái danh mục hàng loạt
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     * @bodyParam status string required Trạng thái: active, inactive. Example: active
     */
    public function bulkUpdateStatus(BulkUpdateStatusPostCategoryRequest $request)
    {
        PostCategory::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return response()->json(['message' => 'Cập nhật trạng thái thành công các danh mục được chọn!']);
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
     * @response 200 {"message": "Post categories imported successfully."}
     */
    public function import(ImportPostCategoryRequest $request)
    {
        Excel::import(new PostCategoriesImport, $request->file('file'));
        return response()->json(['message' => 'Post categories imported successfully.']);
    }

    /**
     * Thay đổi trạng thái danh mục
     *
     * @urlParam category integer required ID danh mục. Example: 1
     * @bodyParam status string required Trạng thái mới: active, inactive. Example: active
     */
    public function changeStatus(ChangeStatusPostCategoryRequest $request, PostCategory $category)
    {
        $category->update(['status' => $request->status]);
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'data'    => new PostCategoryResource($category),
        ]);
    }
}
