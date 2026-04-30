<?php

declare(strict_types=1);

namespace App\Models;

final class User extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE email = :email LIMIT 1', [':email' => $email]);
    }

    public function countAll(): int
    {
        $row = $this->fetchOne('SELECT COUNT(*) AS count FROM users');
        return (int) ($row['count'] ?? 0);
    }

    public function create(string $fullName, string $email, string $passwordHash, string $role = 'user', ?string $profilePicturePath = null): int
    {
        $this->execute(
            'INSERT INTO users (full_name, email, password_hash, role, profile_picture_path) VALUES (:full_name, :email, :password_hash, :role, :profile_picture_path)',
            [
                ':full_name' => $fullName,
                ':email' => $email,
                ':password_hash' => $passwordHash,
                ':role' => $role,
                ':profile_picture_path' => $profilePicturePath,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function idsByRole(string $role): array
    {
        $rows = $this->fetchAll(
            'SELECT id FROM users WHERE role = :role',
            [':role' => $role]
        );

        return array_map(static fn (array $row): int => (int) $row['id'], $rows);
    }

    public function idsNotRole(string $role): array
    {
        $rows = $this->fetchAll(
            'SELECT id FROM users WHERE role <> :role',
            [':role' => $role]
        );

        return array_map(static fn (array $row): int => (int) $row['id'], $rows);
    }
}
