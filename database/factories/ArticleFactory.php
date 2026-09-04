<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'admin_id' => null,
            'article_category_id' => null,
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'is_published' => true,
            'published_at' => now(),
        ];
    }
}
