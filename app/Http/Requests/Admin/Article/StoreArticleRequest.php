<?php

namespace App\Http\Requests\Admin\Article;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'article_category_id' => ['nullable', 'exists:article_categories,id'],
            'new_category_name' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
