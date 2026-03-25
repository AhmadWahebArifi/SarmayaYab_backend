<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'category' => ['nullable', 'string', 'max:255'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'purchase_price' => ['sometimes', 'numeric', 'min:0', 'max:999999999.99'],
            'selling_price' => ['sometimes', 'numeric', 'min:0', 'max:999999999.99'],
            'reorder_point' => ['sometimes', 'integer', 'min:0', 'max:1000000000'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
