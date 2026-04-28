<?php

declare(strict_types=1);

namespace App\Models;

final class User extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE email = :email LIMIT 1', [':email' => $email]);
    }

    public function create(string $fullName, string $email, string $passwordHash, string $role = 'user'): int
    {
        $this->execute(
            'INSERT INTO users (full_name, email, password_hash, role) VALUES (:full_name, :email, :password_hash, :role)',
            [
                ':full_name' => $fullName,
                ':email' => $email,
                ':password_hash' => $passwordHash,
                ':role' => $role,
            ]
        );

        return (int) $this->db->lastInsertId();
    }
}
