<?php

namespace App\Modules\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Modules\Post\Enums\PostStatusEnum;
use App\Modules\Post\Models\Post;
use App\Modules\Post\Requests\StorePostRequest;
use App\Modules\Post\Requests\UpdatePostRequest;
use App\Modules\Post\Requests\BulkDestroyPostRequest;
use App\Modules\Post\Requests\BulkUpdateStatusPostRequest;
use App\Modules\Post\Resources\PostResource;
use App\Modules\Post\Resources\PostCollection;
use App\Modules\Post\Exports\PostsExport;
use App\Modules\Post\Imports\PostsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\Post\Requests\ImportPostRequest;
use App\Modules\Post\Requests\ChangeStatusPostRequest;
use App\Modules\Post\Models\PostAttachment;
use Illuminate\Support\Facades\Storage;

/**
 * @group Post
 *
 * Quản lý bài viết: danh sách, chi tiết, tạo, cập nhật, xóa, thao tác hàng loạt
 */
class PostController extends Controller
{
    /**
     * Thống kê bài viết
     *
     * Tổng số, đang xuất bản (published), không xuất bản (draft, archived). Áp dụng cùng bộ lọc với index.
     *
     * @queryParam search string Từ khóa tìm kiếm (tiêu đề). Example: hello
     * @queryParam status string Lọc theo trạng thái: draft, published, archived.
     * @queryParam category_id integer Lọc bài viết thuộc danh mục (ID). Example: 1
     * @queryParam sort_by string Sắp xếp theo: id, title, created_at, view_count. Example: created_at
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function stats(FilterRequest $request)
    {
        $base = Post::filter($request->all());

        return response()->json([
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('status', PostStatusEnum::Published->value)->count(),
            'inactive' => (clone $base)->where('status', '!=', PostStatusEnum::Published->value)->count(),
        ]);
    }

    /**
     * Danh sách bài viết
     *
     * Lấy danh sách có phân trang, lọc và sắp xếp.
     *
     * @queryParam search string Từ khóa tìm kiếm (tiêu đề). Example: hello
     * @queryParam status string Lọc theo trạng thái: draft, published, archived.
     * @queryParam category_id integer Lọc bài viết thuộc danh mục (ID). Example: 1
     * @queryParam sort_by string Sắp xếp theo: id, title, created_at, view_count. Example: created_at
     * @queryParam sort_order string Thứ tự: asc, desc. Example: desc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 10
     */
    public function index(FilterRequest $request)
    {
        $posts = Post::with('categories')->filter($request->all())
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
        $post->load(['categories', 'attachments']);
        return new PostResource($post);
    }

    /**
     * Tạo bài viết mới
     *
     * @bodyParam title string required Tiêu đề (duy nhất). Example: Bài viết mẫu
     * @bodyParam content string required Nội dung (tối thiểu 10 ký tự). Example: Nội dung bài viết...
     * @bodyParam status string required Trạng thái: draft, published, archived. Example: draft
     * @bodyParam category_ids array Mảng ID danh mục (tối đa 20). Example: [1, 2]
     * @bodyParam images[] file Ảnh đính kèm (jpeg/png/gif/webp, tối đa 10 ảnh, mỗi ảnh ≤ 5MB).
     */
    public function store(StorePostRequest $request)
    {
        $data = collect($request->validated())->except(['images', 'category_ids'])->all();
        $post = Post::create($data);
        $this->syncPostCategories($post, $request->validated());
        $this->savePostAttachments($post, $request->file('images', []));
        $post->load(['categories', 'attachments']);
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
     * @bodyParam category_ids array Mảng ID danh mục (ghi đè danh sách hiện tại).
     * @bodyParam images[] file Ảnh mới (append).
     * @bodyParam remove_attachment_ids array Mảng ID đính kèm cần xóa.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = collect($request->validated())->except(['images', 'remove_attachment_ids', 'category_ids'])->all();
        $post->update($data);
        if (array_key_exists('category_ids', $request->validated())) {
            $this->syncPostCategories($post, $request->validated());
        }
        if ($ids = $request->remove_attachment_ids) {
            PostAttachment::where('post_id', $post->id)->whereIn('id', $ids)->delete();
        }
        $this->savePostAttachments($post, $request->file('images', []));
        $post->load(['categories', 'attachments']);
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
     * Áp dụng cùng bộ lọc với index. Trả về file Excel.
     *
     * @queryParam search string Từ khóa tìm kiếm (tiêu đề).
     * @queryParam status string Lọc theo trạng thái: draft, published, archived.
     * @queryParam category_id integer Lọc bài viết thuộc danh mục (ID).
     * @queryParam sort_by string Sắp xếp theo: id, title, created_at, view_count.
     * @queryParam sort_order string Thứ tự: asc, desc.
     */
    public function export(FilterRequest $request)
    {
        return Excel::download(new PostsExport($request->all()), 'posts.xlsx');
    }

    /**
     * Nhập danh sách bài viết
     *
     * @bodyParam file file required File Excel (xlsx, xls, csv). Cột theo chuẩn export.
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

    /**
     * Tăng lượt xem bài viết (gọi khi người dùng xem chi tiết).
     *
     * @urlParam post integer required ID bài viết. Example: 1
     */
    public function incrementView(Post $post)
    {
        $post->increment('view_count');
        return response()->json([
            'message' => 'Đã cập nhật lượt xem.',
            'view_count' => $post->fresh()->view_count,
        ]);
    }

    /**
     * Đồng bộ danh mục bài viết (quan hệ n-n qua bảng pivot).
     */
    private function syncPostCategories(Post $post, array $validated): void
    {
        $ids = $validated['category_ids'] ?? [];
        $post->categories()->sync($ids);
    }

    /**
     * Lưu file ảnh đính kèm vào storage và tạo bản ghi post_attachments.
     */
    private function savePostAttachments(Post $post, array $files): void
    {
        $sortOrder = $post->attachments()->max('sort_order') ?? 0;
        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }
            $path = $file->store('post-attachments/' . $post->id, 'public');
            PostAttachment::create([
                'post_id'        => $post->id,
                'path'           => $path,
                'disk'           => 'public',
                'original_name'  => $file->getClientOriginalName(),
                'mime_type'      => $file->getMimeType(),
                'size'           => $file->getSize(),
                'sort_order'     => ++$sortOrder,
            ]);
        }
    }
}
