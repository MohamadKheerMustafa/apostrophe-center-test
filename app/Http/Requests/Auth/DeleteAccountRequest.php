<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Hash;

class DeleteAccountRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'The password is required to confirm account deletion.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            if ($user && !Hash::check($this->password, $user->password)) {
                $validator->errors()->add('password', 'The password is incorrect.');
            }
        });
    }
}

