<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('super_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z_]+$/', // Only lowercase and underscores
                'unique:roles,name,NULL,id,guard_name,' . ($this->guard_name ?? 'web')
            ],
            'guard_name' => 'required|in:web,api',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Role Name',
            'guard_name' => 'Guard',
            'permissions' => 'Permissions',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'Role name must contain only lowercase letters and underscores.',
            'name.unique' => 'A role with this name already exists for the selected guard.',
        ];
    }
}
