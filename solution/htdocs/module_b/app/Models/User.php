<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function emailExists(string $email): bool
    {
        $stmt = static::db()->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }
}
