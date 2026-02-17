<?php

namespace Database\Seeders;

use App\Modules\Core\Models\User;
use App\Modules\Post\Models\Post;
use App\Modules\Post\Models\PostCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Thứ tự: User → PostCategory (cây) → Post.
     */
    public function run(): void
    {
        $this->seedUsers();
        $this->seedPostCategories();
        $this->seedPosts();
    }

    /**
     * Tạo user. User đầu tiên (id=1) dùng làm người tạo/sửa cho dữ liệu mẫu.
     */
    protected function seedUsers(): void
    {
        User::factory(10)->create();

        // Gán created_by, updated_by (user 1 tự tham chiếu; các user khác tham chiếu user 1)
        User::where('id', 1)->update(['created_by' => 1, 'updated_by' => 1]);
        User::where('id', '>', 1)->update(['created_by' => 1, 'updated_by' => 1]);
    }

    /**
     * Tạo danh mục tin tức dạng cây: vài danh mục gốc, mỗi gốc có vài danh mục con.
     * Không dùng withoutEvents — Nested Set cần model events để set _lft/_rgt.
     * Sau khi tạo xong, cập nhật created_by/updated_by (khi seed không có auth).
     */
    protected function seedPostCategories(): void
    {
        $user = User::first();
        if (! $user) {
            return;
        }

        $rootNames = ['Tin công nghệ', 'Tin thể thao', 'Tin kinh tế', 'Giải trí', 'Giáo dục'];
        foreach ($rootNames as $index => $name) {
            PostCategory::factory()
                ->create([
                    'name' => $name,
                    'slug' => \Illuminate\Support\Str::slug($name),
                    'sort_order' => $index + 1,
                ]);
        }

        $roots = PostCategory::whereIsRoot()->defaultOrder()->get();

        foreach ($roots as $root) {
            $childCount = rand(2, 3);
            for ($i = 0; $i < $childCount; $i++) {
                $child = PostCategory::factory()->make([
                    'name' => $root->name . ' - ' . fake()->word(),
                ]);
                $child->slug = \Illuminate\Support\Str::slug($child->name) . '-' . uniqid();
                $child->appendToNode($root)->save();
            }
        }

        // Gán created_by, updated_by (khi seed không có auth nên model booted gán null)
        PostCategory::whereNull('created_by')->update([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    /**
     * Tạo bài viết, gán ngẫu nhiên user và danh mục.
     */
    protected function seedPosts(): void
    {
        $users = User::all();
        $categories = PostCategory::all();

        if ($users->isEmpty()) {
            return;
        }

        Post::withoutEvents(function () use ($users, $categories) {
            Post::factory(20)
                ->sequence(
                    fn ($sequence) => [
                        'created_by' => $users->random()->id,
                        'updated_by' => $users->random()->id,
                    ]
                )
                ->create()
                ->each(function (Post $post) use ($categories) {
                    if ($categories->isNotEmpty()) {
                        $post->categories()->sync([$categories->random()->id]);
                    }
                });
        });
    }
}
