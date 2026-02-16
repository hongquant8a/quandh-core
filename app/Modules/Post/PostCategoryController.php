<?php

namespace App\Modules\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
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
 * Quản lý danh mục tin tức phân cấp (cấu trúc cây): danh sách, cây, chi tiết, tạo, cập nhật, xóa, thao tác hàng loạt, xuất/nhập, đổi trạng thái.
 */
class PostCategoryController extends Controller
{
    /**
     * Danh sách danh mục (dạng phẳng, phân trang)
     *
     * Lấy danh sách có phân trang, lọc và sắp xếp (đồng bộ User/Post).
     *
     * @queryParam search string Từ khóa tìm kiếm (tên). Example: tin
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     * @queryParam sort_by string Sắp xếp theo: id, name, sort_order, created_at. Example: sort_order
     * @queryParam sort_order string Thứ tự: asc, desc. Example: asc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function index(FilterRequest $request)
    {
        $categories = PostCategory::with(['creator', 'editor'])
            ->withDepth()
            ->filter($request->all())
            ->paginate($request->limit ?? 10);
        return new PostCategoryCollection($categories);
    }

    /**
     * Cây danh mục (toàn bộ cây, không phân trang)
     *
     * Trả về cấu trúc parent-children để hiển thị tree.
     *
     * @queryParam status string Lọc theo trạng thái: active, inactive.
     */
    public function tree(Request $request)
    {
        $query = PostCategory::query()
            ->when($request->status, fn ($q, $v) => $q->where('status', $v));
        $nodes = $query->defaultOrder()->withDepth()->get();
        $tree = $nodes->toTree();
        return PostCategoryTreeResource::collection($tree);
    }

    /**
     * Chi tiết danh mục
     *
     * @urlParam category integer required ID danh mục. Example: 1
     */
    public function show(PostCategory $category)
    {
        $category = PostCategory::with(['creator', 'editor', 'parent', 'children' => fn ($q) => $q->defaultOrder()])
            ->withDepth()
            ->findOrFail($category->id);
        return new PostCategoryResource($category);
    }

    /**
     * Tạo danh mục mới
     *
     * @bodyParam name string required Tên danh mục. Example: Tin công nghệ
     * @bodyParam slug string Slug (nếu không gửi sẽ tự sinh từ name).
     * @bodyParam description string Mô tả.
     * @bodyParam status string required active, inactive. Example: active
     * @bodyParam sort_order integer Thứ tự. Example: 0
     * @bodyParam parent_id integer ID danh mục cha (để tạo con). Example: null
     */
    public function store(StorePostCategoryRequest $request)
    {
        $data = $request->validated();
        $parentId = $data['parent_id'] ?? null;
        unset($data['parent_id']);

        $category = new PostCategory($data);
        if ($parentId) {
            $parent = PostCategory::findOrFail($parentId);
            $category->appendToNode($parent)->save();
        } else {
            $category->saveAsRoot();
        }

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
     * @bodyParam status string active, inactive.
     * @bodyParam sort_order integer Thứ tự.
     * @bodyParam parent_id integer ID danh mục cha (0 = chuyển thành gốc).
     */
    public function update(UpdatePostCategoryRequest $request, PostCategory $category)
    {
        $data = $request->validated();
        $parentId = array_key_exists('parent_id', $data) ? $data['parent_id'] : null;
        unset($data['parent_id']);

        $category->fill($data);
        if ($parentId !== null) {
            if ((int) $parentId === 0) {
                $category->saveAsRoot();
            } else {
                $parent = PostCategory::findOrFail($parentId);
                if ($parent->id !== $category->parent_id) {
                    $category->appendToNode($parent)->save();
                } else {
                    $category->save();
                }
            }
        } else {
            $category->save();
        }

        return (new PostCategoryResource($category))
            ->additional(['message' => 'Danh mục đã được cập nhật!']);
    }

    /**
     * Xóa danh mục
     *
     * Xóa danh mục sẽ xóa luôn tất cả danh mục con (nested set).
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
     * Cập nhật trạng thái hàng loạt danh mục
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
     * File Excel theo thứ tự cây (cha trước con) để có thể nhập lại.
     */
    public function export()
    {
        return Excel::download(new PostCategoriesExport, 'post-categories.xlsx');
    }

    /**
     * Nhập danh sách danh mục
     *
     * @bodyParam file file required File excel (xlsx, xls, csv). Cột: name, slug, description, status, sort_order, parent_slug.
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
