<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends StoreCategoryRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['parent_id'] = [
            'nullable',
            'exists:categories,id',
            Rule::notIn([$this->route('category')?->id]),
        ];

        return $rules;
    }
}
