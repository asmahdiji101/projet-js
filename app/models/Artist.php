<?php

declare(strict_types=1);

namespace App\Models;

final class Artist extends BaseModel
{
    public function all(): array
    {
        return $this->fetchAll('SELECT * FROM artists ORDER BY created_at DESC');
    }

    public function countAll(): int
    {
        $row = $this->fetchOne('SELECT COUNT(*) AS count FROM artists');

        return (int) ($row['count'] ?? 0);
    }

    public function create(string $name, string $slug, string $description, ?string $imagePath = null): int
    {
        $this->execute(
            'INSERT INTO artists (name, slug, description, image_path) VALUES (:name, :slug, :description, :image_path)',
            [
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

    public function update(int $id, string $name, string $slug, string $description, ?string $imagePath = null): bool
    {
        return $this->execute(
            'UPDATE artists SET name = :name, slug = :slug, description = :description, image_path = COALESCE(:image_path, image_path) WHERE id = :id',
            [
                ':id' => $id,
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
