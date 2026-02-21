<?php

namespace App\Modules\Post\Services;

use App\Modules\Post\Enums\PostStatusEnum;
use App\Modules\Post\Exports\PostsExport;
use App\Modules\Post\Imports\PostsImport;
use App\Modules\Post\Models\Post;
use Illuminate\Support\Facades\Storage;
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
        return $post->load(['categories', 'media']);
    }

    public function store(array $validated, array $images = []): Post
    {
        $storedFiles = [];

        try {
            return DB::transaction(function () use ($validated, $images, &$storedFiles) {
                $data = collect($validated)->except(['images', 'category_ids'])->all();
                $post = Post::create($data);

                $this->syncPostCategories($post, $validated);
                $this->savePostAttachments($post, $images, $storedFiles);

                return $post->load(['categories', 'media']);
            });
        } catch (\Throwable $exception) {
            $this->cleanupStoredMediaFiles($storedFiles);
            throw $exception;
        }
    }

    public function update(Post $post, array $validated, array $images = []): Post
    {
        $storedFiles = [];

        try {
            return DB::transaction(function () use ($post, $validated, $images, &$storedFiles) {
                $data = collect($validated)->except(['images', 'remove_attachment_ids', 'category_ids'])->all();
                $post->update($data);

                if (array_key_exists('category_ids', $validated)) {
                    $this->syncPostCategories($post, $validated);
                }

                if (! empty($validated['remove_attachment_ids'])) {
                    $post->media()
                        ->where('collection_name', 'post-attachments')
                        ->whereIn('id', $validated['remove_attachment_ids'])
                        ->get()
                        ->each
                        ->delete();
                }

                $this->savePostAttachments($post, $images, $storedFiles);

                return $post->load(['categories', 'media']);
            });
        } catch (\Throwable $exception) {
            $this->cleanupStoredMediaFiles($storedFiles);
            throw $exception;
        }
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

        return $post->load(['categories', 'media']);
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

    private function savePostAttachments(Post $post, array $files, array &$storedFiles): void
    {
        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $media = $post->addMedia($file)
                ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName($file->hashName())
                ->withCustomProperties([
                    'original_name' => $file->getClientOriginalName(),
                ])
                ->toMediaCollection('post-attachments', 'public');

            $storedFiles[] = [
                'disk' => $media->disk,
                'path' => $media->getPathRelativeToRoot(),
            ];
        }
    }

    private function cleanupStoredMediaFiles(array $storedFiles): void
    {
        foreach ($storedFiles as $storedFile) {
            if (! empty($storedFile['disk']) && ! empty($storedFile['path'])) {
                Storage::disk($storedFile['disk'])->delete($storedFile['path']);
            }
        }
    }
}
