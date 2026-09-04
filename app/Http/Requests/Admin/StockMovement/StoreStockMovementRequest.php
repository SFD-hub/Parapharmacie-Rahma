<?php

namespace App\Http\Requests\Admin\StockMovement;

use App\Enums\StockMovementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreStockMovementRequest extends FormRequest
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
            'type' => [
                'required',
                new Enum(StockMovementType::class),
                // "Sale" is only ever created automatically by a real purchase.
                Rule::notIn([StockMovementType::Sale->value]),
            ],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'comment' => ['nullable', 'string', 'max:255'],
        ];
    }
}
