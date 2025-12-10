<?php

namespace App\Http\Requests\Users;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDesignRequest extends FormRequest
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
            'name' => 'sometimes|array',
            'name.ar' => 'required_with:name|string|max:255',
            'name.en' => 'required_with:name|string|max:255',

            'description' => 'sometimes|array',
            'description.ar' => 'required_with:description|string|max:1000',
            'description.en' => 'required_with:description|string|max:1000',

            'price' => 'sometimes|numeric|min:0',

            'images' => 'sometimes|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

            'measurements' => 'sometimes|array',
            'measurements.*' => 'required|exists:measurements,size',

            'design_options' => 'sometimes|array',
            'design_options.type.*' => 'required|string',
            'design_options.name' => 'required_with:design_options|array',
            'design_options.name.en.*' => 'sometimes|string',
            'design_options.name.ar.*' => 'sometimes|string',
        ];
    }

}
