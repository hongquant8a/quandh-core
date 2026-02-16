<?php

namespace App\Modules\Post\Exports;

use App\Modules\Post\Models\Post;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PostsExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected array $filters = []
    ) {}

    /**
     * Xuất theo bộ lọc của index.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Post::filter($this->filters)->get(['id', 'title', 'content', 'created_by', 'created_at']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Content',
            'Author ID',
            'Created At',
        ];
    }
}
