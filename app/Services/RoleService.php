<?php

namespace App\Services;

use App\ApiCode;
use App\Http\Resources\Users\UserResource;
use App\Interfaces\RoleServiceInterface;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleService implements RoleServiceInterface
{
    public function assignAdminRole(User $user): array
    {
        if ($user->isAdmin()) {
            return [
                'data' => null,
                'message' => 'User already has admin role',
                'statusCode' => ApiCode::BAD_REQUEST
            ];
        }

        return DB::transaction(function () use ($user) {
            $user->update(['role' => Role::ADMIN]);
            $user->refresh();

            return [
                'data' => new UserResource($user),
                'message' => 'Admin role assigned successfully',
                'statusCode' => ApiCode::SUCCESS
            ];
        });
    }

    public function revokeAdminRole(User $user): array
    {
        if ($user->isUser()) {
            return [
                'data' => null,
                'message' => 'User already has user role',
                'statusCode' => ApiCode::BAD_REQUEST
            ];
        }

        return DB::transaction(function () use ($user) {
            $user->update(['role' => Role::USER]);
            $user->refresh();

            return [
                'data' => new UserResource($user),
                'message' => 'Admin role revoked successfully',
                'statusCode' => ApiCode::SUCCESS
            ];
        });
    }
}

