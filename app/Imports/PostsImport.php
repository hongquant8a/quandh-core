<?php

namespace App\Imports;

use App\Models\Post;
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
        // created_by and updated_by will be handled by the Post model's observer
        return new Post([
            'title'   => $row['title'],
            'content' => $row['content'],
            'status'  => $row['status'] ?? 'published',
        ]);
    }
}
