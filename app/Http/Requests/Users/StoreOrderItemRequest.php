<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderItemRequest extends FormRequest
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
            'design_id' => 'required|exists:designs,id',
            'measurement_id' => 'required|exists:measurements,id',
            'quantity' => 'required|integer|min:1|max:100',
            'design_options' => 'required|array',
            'design_options.*.type' => 'required_with:design_options|string|in:color,dome_type,sleeve_type,fabric_type',
            'design_options.*.name' => 'required_with:design_options|array',
            'design_options.*.name.en' => 'nullable|string',
            'design_options.*.name.ar' => 'nullable|string',
        ];
    }
}
