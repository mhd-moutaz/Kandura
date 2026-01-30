<?php

namespace App\Http\Requests\Global;

use Illuminate\Foundation\Http\FormRequest;

class SearchDesignsRequest extends FormRequest
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
            // البحث
            'search' => ['nullable', 'string', 'max:255'],

            // الفلاتر
            'size' => ['nullable'],

            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],

            'design_option' => ['nullable'],

            'creator' => ['nullable'],

            // الترتيب
            'sort_by' => ['nullable', 'string', 'in:id,name,price,created_at,updated_at'],
            'sort_dir' => ['nullable', 'string', 'in:asc,desc'],

            // Pagination
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
