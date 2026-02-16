<?php

namespace App\Modules\Post\Exports;

use App\Modules\Post\Models\PostCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PostCategoriesExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected array $filters = []
    ) {}

    /**
     * Xuất danh mục theo bộ lọc của index, thứ tự cây (cha trước con) để import lại đúng cấu trúc.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $nodes = PostCategory::filter($this->filters)->defaultOrder()->withDepth()->get();
        return $nodes->map(function (PostCategory $category) {
            return [
                'id'          => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
                'description' => $category->description,
                'status'      => $category->status,
                'sort_order'  => $category->sort_order,
                'parent_slug' => $category->parent_id ? (PostCategory::find($category->parent_id)?->slug ?? '') : '',
                'depth'       => $category->depth,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Slug',
            'Description',
            'Status',
            'Sort Order',
            'Parent Slug',
            'Depth',
        ];
    }
}
