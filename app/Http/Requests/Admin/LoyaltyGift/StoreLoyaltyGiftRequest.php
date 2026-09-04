<?php

namespace App\Http\Requests\Admin\LoyaltyGift;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoyaltyGiftRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id'],
            'points_cost' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
