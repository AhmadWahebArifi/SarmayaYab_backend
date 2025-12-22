<?php

namespace App\Http\Requests\Investment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvestmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'expected_return' => ['sometimes', 'numeric', 'min:0', 'max:999999999.99'],
            'actual_return' => ['sometimes', 'numeric', 'min:0', 'max:999999999.99'],
            'end_date' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', 'in:active,completed,cancelled'],
            'type' => ['sometimes', 'in:stocks,bonds,real_estate,crypto,other'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }
}
