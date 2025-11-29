<?php

namespace App\Models;

class Role
{
    public const USER = 'user';
    public const ADMIN = 'admin';

    /**
     * Get all available roles.
     *
     * @return array
     */
    public static function all(): array
    {
        return [
            self::USER,
            self::ADMIN,
        ];
    }
}

