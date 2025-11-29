<?php

namespace App\Interfaces;

interface UserServiceInterface
{
    public function getUsers(array $filters): array;
}
