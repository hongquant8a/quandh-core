<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostCollection;
use App\Http\Requests\FilterRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\BulkPostRequest;

class PostController extends Controller
{

    public function index(FilterRequest $request)
    {
        $posts = Post::filter($request->all())
                ->paginate($request->limit ?? 10);
        return new PostCollection($posts);
    }

    public function show(Post $post) { 
        return new PostResource($post);
    }

    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->validated());
        return (new PostResource($post))
                ->additional(['message' => 'Bài viết đã được tạo thành công!']);
    }

    public function update(UpdatePostRequest $request, Post $post) {
        $post->update($request->validated());
        return new PostResource($post);
    }

    public function destroy(Post $post) {
        $post->delete();
        return response()->json(['message' => 'Bài viết đã được xóa thành công!']);
    }

    public function bulkDestroy(BulkPostRequest $request) {
        Post::destroy($request->ids);
        return response()->json(['message' => 'Đã xóa thành công các bài viết được chọn!']);
    }

    public function bulkUpdateStatus(BulkPostRequest $request) {
        Post::whereIn('id', $request->ids)->update(['status' => $request->status]);
        return response()->json(['message' => 'Cập nhật trạng thái thành công các bài viết được chọn!']);
    }
}
