<?php

namespace App\Http\Requests\Admin\Loyalty;

use App\Enums\LoyaltyMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateLoyaltySettingsRequest extends FormRequest
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
            'enabled' => ['nullable', 'boolean'],
            'amount_per_point' => ['required', 'numeric', 'min:1'],
            'points_per_amount' => ['required', 'integer', 'min:1'],
            'point_value' => ['required', 'numeric', 'min:0.01'],
            'max_usage_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'mode' => ['required', new Enum(LoyaltyMode::class)],
        ];
    }
}
