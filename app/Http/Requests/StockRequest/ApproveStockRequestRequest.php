<?php

namespace App\Http\Requests\StockRequest;

use Illuminate\Foundation\Http\FormRequest;

class ApproveStockRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'exists:stock_request_items,product_id'],
            'items.*.approved_qty' => ['required', 'integer', 'min:0'],
        ];
    }
}
