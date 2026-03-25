<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'sku' => ['sometimes', 'string', 'max:100', 'unique:products,sku,' . $productId],
            'category' => ['sometimes', 'nullable', 'string', 'max:255'],
            'supplier' => ['sometimes', 'nullable', 'string', 'max:255'],
            'purchase_price' => ['sometimes', 'numeric', 'min:0', 'max:999999999.99'],
            'selling_price' => ['sometimes', 'numeric', 'min:0', 'max:999999999.99'],
            'reorder_point' => ['sometimes', 'integer', 'min:0', 'max:1000000000'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
