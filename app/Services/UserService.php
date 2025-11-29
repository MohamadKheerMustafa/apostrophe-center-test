<?php

namespace App\Services;

use App\ApiCode;
use App\Http\Resources\Users\UserCollection;
use App\Interfaces\UserServiceInterface;
use App\Models\User;

class UserService implements UserServiceInterface
{
    public function getUsers(array $filters): array
    {
        $query = User::query();

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allowedColumns = ['id', 'name', 'email', 'created_at', 'updated_at'];
        $orderBy = $filters['order_by'] ?? 'id';
        $orderDirection = $filters['order_direction'] ?? 'asc';

        if (!in_array($orderBy, $allowedColumns, true)) {
            $orderBy = 'id';
        }

        $orderDirection = strtolower($orderDirection) === 'desc' ? 'desc' : 'asc';

        $query->orderBy($orderBy, $orderDirection);

        $perPage = $filters['per_page'] ?? 15;
        $page = $filters['page'] ?? null;
        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => new UserCollection($users),
            'message' => 'Users retrieved successfully',
            'statusCode' => ApiCode::SUCCESS
        ];
    }
}
