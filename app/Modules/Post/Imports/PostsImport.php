<?php

namespace App\Modules\Post\Imports;

use App\Modules\Post\Models\Post;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PostsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Post([
            'title'   => $row['title'],
            'content' => $row['content'],
            'status'  => $row['status'] ?? 'published',
        ]);
    }
}
