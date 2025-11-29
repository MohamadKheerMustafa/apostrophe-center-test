<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class GetUsersRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'string', 'max:255'],
            'order_by' => ['sometimes', 'string', 'in:id,name,email,created_at,updated_at'],
            'order_direction' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }

    public function messages(): array
    {
        return [
            'page.integer' => 'The page must be an integer.',
            'page.min' => 'The page must be at least 1.',
            'per_page.integer' => 'The per_page must be an integer.',
            'per_page.min' => 'The per_page must be at least 1.',
            'per_page.max' => 'The per_page may not be greater than 100.',
            'search.string' => 'The search must be a string.',
            'search.max' => 'The search may not be greater than 255 characters.',
            'order_by.in' => 'The order_by field must be one of: id, name, email, created_at, updated_at.',
            'order_direction.in' => 'The order_direction must be either asc or desc.',
        ];
    }
}

