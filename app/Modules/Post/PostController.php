<?php

namespace App\Modules\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Models\Post;
use App\Modules\Post\Requests\StorePostRequest;
use App\Modules\Post\Requests\UpdatePostRequest;
use App\Modules\Post\Requests\BulkDestroyPostRequest;
use App\Modules\Post\Requests\BulkUpdateStatusPostRequest;
use App\Modules\Post\Resources\PostResource;
use App\Modules\Post\Resources\PostCollection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PostsExport;
use App\Imports\PostsImport;
use App\Modules\Post\Requests\ImportPostRequest;
use App\Modules\Post\Requests\ChangeStatusPostRequest;

/**
 * @group Post
 *
 * Quản lý bài viết: danh sách, chi tiết, tạo, cập nhật, xóa, thao tác hàng loạt
 */
class PostController extends Controller
{
    /**
     * Danh sách bài viết
     *
     * Lấy danh sách có phân trang, lọc và sắp xếp.
     *
     * @queryParam search string Từ khóa tìm kiếm (tiêu đề). Example: hello
     * @queryParam status string Lọc theo trạng thái: draft, published, archived.
     * @queryParam sort_by string Sắp xếp theo: id, title, name, created_at. Example: created_at
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function index(FilterRequest $request)
    {
        $posts = Post::filter($request->all())
            ->paginate($request->limit ?? 10);
        return new PostCollection($posts);
    }

    /**
     * Chi tiết bài viết
     *
     * @urlParam post integer required ID bài viết. Example: 1
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Tạo bài viết mới
     *
     * @bodyParam title string required Tiêu đề (duy nhất). Example: Bài viết mẫu
     * @bodyParam content string required Nội dung (tối thiểu 10 ký tự). Example: Nội dung bài viết...
     * @bodyParam status string required Trạng thái: draft, published, archived. Example: draft
     */
    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->validated());
        return (new PostResource($post))
            ->additional(['message' => 'Bài viết đã được tạo thành công!']);
    }

    /**
     * Cập nhật bài viết
     *
     * @urlParam post integer required ID bài viết. Example: 1
     * @bodyParam title string Tiêu đề (duy nhất).
     * @bodyParam content string Nội dung (tối thiểu 10 ký tự).
     * @bodyParam status string Trạng thái: draft, published, archived.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());
        return new PostResource($post);
    }

    /**
     * Xóa bài viết
     *
     * @urlParam post integer required ID bài viết. Example: 1
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Bài viết đã được xóa thành công!']);
    }

    /**
     * Xóa hàng loạt bài viết
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     */
    public function bulkDestroy(BulkDestroyPostRequest $request)
    {
        Post::destroy($request->ids);
        return response()->json(['message' => 'Đã xóa thành công các bài viết được chọn!']);
    }

    /**
     * Cập nhật trạng thái hàng loạt bài viết
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     * @bodyParam status string required Trạng thái: draft, published, archived. Example: published
     */
    public function bulkUpdateStatus(BulkUpdateStatusPostRequest $request)
    {
        Post::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return response()->json(['message' => 'Cập nhật trạng thái thành công các bài viết được chọn!']);
    }

    /**
     * Xuất danh sách bài viết
     *
     * @authenticated
     */
    public function export()
    {
        return Excel::download(new PostsExport, 'posts.xlsx');
    }

    /**
     * Nhập danh sách bài viết
     *
     * @authenticated
     * @bodyParam file file required File excel (xlsx, xls, csv).
     * @response 200 {"message": "Posts imported successfully."}
     */
    public function import(ImportPostRequest $request)
    {
        Excel::import(new PostsImport, $request->file('file'));

        return response()->json(['message' => 'Posts imported successfully.']);
    }

    /**
     * Thay đổi trạng thái bài viết
     *
     * @urlParam post integer required ID bài viết. Example: 1
     * @bodyParam status string required Trạng thái mới: draft, published, archived. Example: published
     */
    public function changeStatus(ChangeStatusPostRequest $request, Post $post)
    {
        $post->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'data' => new PostResource($post)
        ]);
    }
}
