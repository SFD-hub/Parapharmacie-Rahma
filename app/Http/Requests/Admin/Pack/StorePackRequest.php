<?php

namespace App\Http\Requests\Admin\Pack;

use App\Enums\PackBadge;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePackRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'badge' => ['nullable', new Enum(PackBadge::class)],
            'is_featured_home' => ['nullable', 'boolean'],
            'is_featured_recommendations' => ['nullable', 'boolean'],
            'loyalty_bonus_points' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'max_sales' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
