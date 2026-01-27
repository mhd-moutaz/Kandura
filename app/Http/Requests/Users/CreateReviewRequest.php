<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class CreateReviewRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'التقييم مطلوب / Rating is required',
            'rating.integer' => 'يجب أن يكون التقييم رقماً صحيحاً / Rating must be an integer',
            'rating.min' => 'يجب أن يكون التقييم على الأقل 1 / Rating must be at least 1',
            'rating.max' => 'يجب أن يكون التقييم على الأكثر 5 / Rating must be at most 5',
            'comment.string' => 'يجب أن يكون التعليق نصاً / Comment must be a string',
            'comment.max' => 'يجب ألا يتجاوز التعليق 1000 حرف / Comment must not exceed 1000 characters',
        ];
    }
}
