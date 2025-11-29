<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseFormRequest;

class AssignRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.integer' => 'The user ID must be an integer.',
            'user_id.exists' => 'The selected user does not exist.',
        ];
    }
}

