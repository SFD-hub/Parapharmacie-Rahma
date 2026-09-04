<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Validation\Rule;

class UpdateProductRequest extends StoreProductRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['sku'] = ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->route('product'))];

        return $rules;
    }
}
