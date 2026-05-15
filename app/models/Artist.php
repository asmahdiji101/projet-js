<?php

declare(strict_types=1);

namespace App\Models;

final class Artist extends BaseModel
{
    public function all(): array
    {
        return $this->fetchAll(
            'SELECT artists.*, users.full_name AS user_full_name, users.email AS user_email, users.profile_picture_path AS user_profile_picture_path
             FROM artists
             LEFT JOIN users ON users.id = artists.user_id
             ORDER BY artists.created_at DESC'
        );
    }

    public function countAll(): int
    {
        $row = $this->fetchOne('SELECT COUNT(*) AS count FROM artists');

        return (int) ($row['count'] ?? 0);
    }

    public function create(string $name, string $slug, string $description, ?string $imagePath = null, ?int $userId = null): int
    {
        $this->execute(
            'INSERT INTO artists (user_id, name, slug, description, image_path) VALUES (:user_id, :name, :slug, :description, :image_path)',
            [
                ':user_id' => $userId,
                ':name' => $name,
                ':slug' => $slug,
                ':description' => $description,
                ':image_path' => $imagePath,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM artists WHERE id = :id LIMIT 1', [':id' => $id]);
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->fetchOne('SELECT * FROM artists WHERE user_id = :user_id LIMIT 1', [':user_id' => $userId]);
    }

    public function update(int $id, string $name, string $slug, string $description, ?string $imagePath = null, ?int $userId = null): bool
    {
        return $this->execute(
            'UPDATE artists SET user_id = COALESCE(:user_id, user_id), name = :name, slug = :slug, description = :description, image_path = COALESCE(:image_path, image_path) WHERE id = :id',
            [
                ':id' => $id,
                ':user_id' => $userId,
                ':name' => $name,
                ':slug' => $slug,
                ':description' => $description,
                ':image_path' => $imagePath,
            ]
        );
    }

    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM artists WHERE id = :id', [':id' => $id]);
    }
}
