<?php

namespace App\Http\Requests\Admin\ProductComplement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductComplementRequest extends FormRequest
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
        $product = $this->route('product');

        return [
            'complementary_product_id' => [
                'required',
                'exists:products,id',
                Rule::notIn([$product->id]),
                Rule::unique('product_complements', 'complementary_product_id')->where('product_id', $product->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'complementary_product_id.not_in' => 'Un produit ne peut pas être son propre complément.',
            'complementary_product_id.unique' => 'Ce produit complémentaire est déjà associé.',
        ];
    }
}
