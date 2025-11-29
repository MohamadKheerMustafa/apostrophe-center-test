<?php

namespace App\Interfaces;

use App\Models\User;

interface RoleServiceInterface
{
    public function assignAdminRole(User $user): array;
    public function revokeAdminRole(User $user): array;
}
