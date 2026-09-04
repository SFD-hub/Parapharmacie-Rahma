<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleCategorySeeder extends Seeder
{
    /**
     * Seed the demonstration blog categories.
     */
    public function run(): void
    {
        $categories = ['Soin du visage', 'Soin du corps', 'Conseils beauté'];

        foreach ($categories as $name) {
            ArticleCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
