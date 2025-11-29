<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\GetUsersRequest;
use App\Interfaces\UserServiceInterface;

class UserController extends AppBaseController
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index(GetUsersRequest $request)
    {
        $result = $this->userService->getUsers($request->validated());

        return $this->handleResponse(
            $result['statusCode'],
            $result['data'],
            $result['message']
        );
    }
}

