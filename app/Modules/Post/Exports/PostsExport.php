<?php

namespace App\Modules\Post\Exports;

use App\Modules\Post\Models\Post;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PostsExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Post::all(['id', 'title', 'content', 'created_by', 'created_at']);
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
