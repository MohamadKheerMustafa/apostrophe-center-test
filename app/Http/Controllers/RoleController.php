<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\AssignRoleRequest;
use App\Interfaces\RoleServiceInterface;
use App\Models\User;

class RoleController extends AppBaseController
{
    private RoleServiceInterface $roleService;

    public function __construct(RoleServiceInterface $roleService)
    {
        $this->roleService = $roleService;
    }

    public function assignAdmin(AssignRoleRequest $request)
    {
        $user = User::findOrFail($request->validated()['user_id']);

        $result = $this->roleService->assignAdminRole($user);

        return $this->handleResponse(
            $result['statusCode'],
            $result['data'],
            $result['message']
        );
    }

    public function revokeAdmin(AssignRoleRequest $request)
    {
        $currentUser = $request->user();
        $user = User::findOrFail($request->validated()['user_id']);

        if ($currentUser->id === $user->id) {
            return $this->handleResponse(
                \App\ApiCode::BAD_REQUEST,
                null,
                'You cannot revoke your own admin role'
            );
        }

        $result = $this->roleService->revokeAdminRole($user);

        return $this->handleResponse(
            $result['statusCode'],
            $result['data'],
            $result['message']
        );
    }
}
