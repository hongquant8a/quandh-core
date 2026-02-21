<?php

namespace App\Modules\Post\Services;

use App\Modules\Post\Enums\PostStatusEnum;
use App\Modules\Post\Exports\PostsExport;
use App\Modules\Post\Imports\PostsImport;
use App\Modules\Post\Models\Post;
use App\Modules\Post\Models\PostAttachment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PostService
{
    public function stats(array $filters): array
    {
        $base = Post::filter($filters);

        return [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('status', PostStatusEnum::Published->value)->count(),
            'inactive' => (clone $base)->where('status', '!=', PostStatusEnum::Published->value)->count(),
        ];
    }

    public function index(array $filters, int $limit)
    {
        return Post::with('categories')
            ->filter($filters)
            ->paginate($limit);
    }

    public function show(Post $post): Post
    {
        return $post->load(['categories', 'attachments']);
    }

    public function store(array $validated, array $images = []): Post
    {
        return DB::transaction(function () use ($validated, $images) {
            $data = collect($validated)->except(['images', 'category_ids'])->all();
            $post = Post::create($data);

            $this->syncPostCategories($post, $validated);
            $this->savePostAttachments($post, $images);

            return $post->load(['categories', 'attachments']);
        });
    }

    public function update(Post $post, array $validated, array $images = []): Post
    {
        return DB::transaction(function () use ($post, $validated, $images) {
            $data = collect($validated)->except(['images', 'remove_attachment_ids', 'category_ids'])->all();
            $post->update($data);

            if (array_key_exists('category_ids', $validated)) {
                $this->syncPostCategories($post, $validated);
            }

            if (! empty($validated['remove_attachment_ids'])) {
                PostAttachment::where('post_id', $post->id)
                    ->whereIn('id', $validated['remove_attachment_ids'])
                    ->delete();
            }

            $this->savePostAttachments($post, $images);

            return $post->load(['categories', 'attachments']);
        });
    }

    public function destroy(Post $post): void
    {
        $post->delete();
    }

    public function bulkDestroy(array $ids): void
    {
        Post::destroy($ids);
    }

    public function bulkUpdateStatus(array $ids, string $status): void
    {
        Post::whereIn('id', $ids)->update(['status' => $status]);
    }

    public function export(array $filters): BinaryFileResponse
    {
        return Excel::download(new PostsExport($filters), 'posts.xlsx');
    }

    public function import($file): void
    {
        Excel::import(new PostsImport(), $file);
    }

    public function changeStatus(Post $post, string $status): Post
    {
        $post->update(['status' => $status]);

        return $post->load(['categories', 'attachments']);
    }

    public function incrementView(Post $post): int
    {
        $post->increment('view_count');

        return (int) $post->fresh()->view_count;
    }

    private function syncPostCategories(Post $post, array $validated): void
    {
        $ids = $validated['category_ids'] ?? [];
        $post->categories()->sync($ids);
    }

    private function savePostAttachments(Post $post, array $files): void
    {
        $sortOrder = $post->attachments()->max('sort_order') ?? 0;

        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('post-attachments/' . $post->id, 'public');

            PostAttachment::create([
                'post_id' => $post->id,
                'path' => $path,
                'disk' => 'public',
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'sort_order' => ++$sortOrder,
            ]);
        }
    }
}
