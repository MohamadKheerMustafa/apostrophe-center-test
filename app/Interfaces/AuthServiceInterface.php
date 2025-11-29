<?php

namespace App\Interfaces;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(array $data): array;
    public function login(array $credentials): array;
    public function refresh(): array;
    public function me(User $user): array;
    public function updateProfile(User $user, array $data): array;
    public function deleteAccount(User $user): array;
}
