<?php

namespace App\Http\Requests\Users;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class StoreDesignRequest extends FormRequest
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
            'name' => 'required|array',
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',

            'description' => 'required|array',
            'description.ar' => 'required|string|max:1000',
            'description.en' => 'required|string|max:1000',

            'price' => 'required|numeric|min:0',

            'quantity' => 'sometimes|integer|min:0|max:999999',

            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

            'measurements' => 'required|array',
            'measurements.*' => 'required|exists:measurements,size',

            'design_options' => 'required|array',
            'design_options.type.*' => 'required|string',
            'design_options.name' => 'required|array',
            'design_options.name.en.*' => 'sometimes|string',
            'design_options.name.ar.*' => 'sometimes|string',
        ];
    }

}
