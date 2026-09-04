<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'ingredients' => ['nullable', 'string'],
            'usage_instructions' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_new' => ['sometimes', 'boolean'],
            'is_loyalty_featured' => ['sometimes', 'boolean'],
            // "is_best_seller" n'est plus soumis depuis le formulaire : il
            // est recalculé automatiquement à partir des ventes réelles
            // (voir BestSellerService), jamais depuis une saisie admin.
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'catégorie',
            'brand_id' => 'marque',
            'sale_price' => 'prix promotionnel',
        ];
    }
}
