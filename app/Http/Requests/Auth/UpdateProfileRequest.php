<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $user = $this->user();
        $userId = $user ? $user->id : null;

        $rules = [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
        ];

        if ($this->filled('password')) {
            $rules['old_password'] = ['required', 'string'];
            $rules['password'] = [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'old_password.required' => 'The old password is required when changing password.',
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    public function withValidator($validator)
    {
        if ($this->filled('password') && $this->filled('old_password')) {
            $validator->after(function ($validator) {
                $user = $this->user();

                if (!$user) {
                    return;
                }

                if (!Hash::check($this->old_password, $user->password)) {
                    $validator->errors()->add('old_password', 'The old password is incorrect.');
                }

                if (Hash::check($this->password, $user->password)) {
                    $validator->errors()->add('password', 'The new password must be different from the old password.');
                }
            });
        }
    }
}

