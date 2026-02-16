<?php

namespace App\Modules\Post\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => Str::slug($this->title),
            'content'     => $this->content,
            'status'      => $this->status,
            'category_id' => $this->category_id,
            'category'    => $this->whenLoaded('category', fn () => new PostCategoryResource($this->category)),
            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(fn ($a) => [
                    'id'       => $a->id,
                    'url'      => $a->url,
                    'original_name' => $a->original_name,
                    'mime_type' => $a->mime_type,
                    'size'     => $a->size,
                    'sort_order' => $a->sort_order,
                ]);
            }),
            'created_by'  => $this->creator->name ?? 'N/A',
            'updated_by'  => $this->editor->name ?? 'N/A',
            'created_at'  => $this->created_at->toDateTimeString(),
            'updated_at'  => $this->updated_at->toDateTimeString(),
        ];
    }
}
