<?php

namespace App\Http\Requests\StockRequest;

use Illuminate\Foundation\Http\FormRequest;

class DispatchStockRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
