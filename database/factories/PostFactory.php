<?php

namespace Database\Factories;

use App\Modules\Post\Models\Post;
use App\Modules\Post\Models\PostCategory;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Post\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'category_id' => null,
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    /**
     * Trạng thái published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'published']);
    }

    /**
     * Trạng thái draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'draft']);
    }

    /**
     * Thuộc danh mục (dùng trong seeder hoặc test).
     */
    public function forCategory(PostCategory $category): static
    {
        return $this->state(fn (array $attributes) => ['category_id' => $category->id]);
    }

    /**
     * Gán người tạo/sửa (dùng trong seeder).
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }
}
