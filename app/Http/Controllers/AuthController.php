<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\DeleteAccountRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Interfaces\AuthServiceInterface;
use Illuminate\Http\Request;

class AuthController extends AppBaseController
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return $this->handleResponse(
            $result['statusCode'],
            $result['data'],
            $result['message']
        );
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        return $this->handleResponse(
            $result['statusCode'],
            $result['data'],
            $result['message']
        );
    }

    public function refresh()
    {
        $result = $this->authService->refresh();

        return $this->handleResponse(
            $result['statusCode'],
            $result['data'],
            $result['message']
        );
    }

    public function me(Request $request)
    {
        $user = $request->user();

        $result = $this->authService->me($user);

        return $this->handleResponse(
            $result['statusCode'],
            $result['data'],
            $result['message']
        );
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $data = $request->validated();
        unset($data['old_password']);

        $result = $this->authService->updateProfile($user, $data);

        return $this->handleResponse(
            $result['statusCode'],
            $result['data'],
            $result['message']
        );
    }

    public function deleteAccount(DeleteAccountRequest $request)
    {
        $user = $request->user();

        $result = $this->authService->deleteAccount($user);

        return $this->handleResponse(
            $result['statusCode'],
            $result['data'],
            $result['message']
        );
    }
}
