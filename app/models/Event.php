<?php

declare(strict_types=1);

namespace App\Models;

final class Event extends BaseModel
{
    public function allPublished(): array
    {
        return $this->fetchAll(
            'SELECT events.*, COALESCE(artists.name, users.full_name, "Artist") AS artist_name
             FROM events
             LEFT JOIN artists ON artists.id = events.artist_id
             LEFT JOIN users ON users.id = events.user_artist_id
             WHERE events.status = :status AND events.approval_status = :approval
             ORDER BY events.event_date ASC',
            [':status' => 'published', ':approval' => 'approved']
        );
    }

    public function findPublishedById(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT events.*, COALESCE(artists.name, users.full_name, "Artist") AS artist_name
             FROM events
             LEFT JOIN artists ON artists.id = events.artist_id
             LEFT JOIN users ON users.id = events.user_artist_id
             WHERE events.id = :id AND events.status = :status
             LIMIT 1',
            [':id' => $id, ':status' => 'published']
        );
    }

    public function countAll(): int
    {
        $row = $this->fetchOne('SELECT COUNT(*) AS count FROM events');

        return (int) ($row['count'] ?? 0);
    }

    public function all(): array
    {
        return $this->fetchAll(
            'SELECT events.*, COALESCE(artists.name, users.full_name, "Artist") AS artist_name
             FROM events
             LEFT JOIN artists ON artists.id = events.artist_id
             LEFT JOIN users ON users.id = events.user_artist_id
             ORDER BY events.created_at DESC'
        );
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM events WHERE id = :id LIMIT 1', [':id' => $id]);
    }

    public function create(?int $artistId, string $title, string $slug, string $description, string $eventDate, string $location, ?string $imagePath, string $status = 'published', string $approvalStatus = 'approved', ?int $userArtistId = null): int
    {
        $this->execute(
            'INSERT INTO events (artist_id, user_artist_id, title, slug, description, event_date, location, image_path, status, approval_status) VALUES (:artist_id, :user_artist_id, :title, :slug, :description, :event_date, :location, :image_path, :status, :approval_status)',
            [
                ':artist_id' => $artistId,
                ':user_artist_id' => $userArtistId,
                ':title' => $title,
                ':slug' => $slug,
                ':description' => $description,
                ':event_date' => $eventDate,
                ':location' => $location,
                ':image_path' => $imagePath,
                ':status' => $status,
                ':approval_status' => $approvalStatus,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, ?int $artistId, string $title, string $slug, string $description, string $eventDate, string $location, ?string $imagePath, string $status): bool
    {
        return $this->execute(
            'UPDATE events SET artist_id = :artist_id, title = :title, slug = :slug, description = :description, event_date = :event_date, location = :location, image_path = COALESCE(:image_path, image_path), status = :status WHERE id = :id',
            [
                ':id' => $id,
                ':artist_id' => $artistId,
                ':title' => $title,
                ':slug' => $slug,
                ':description' => $description,
                ':event_date' => $eventDate,
                ':location' => $location,
                ':image_path' => $imagePath,
                ':status' => $status,
            ]
        );
    }

    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM events WHERE id = :id', [':id' => $id]);
    }

    public function byArtistUser(int $userId): array
    {
        return $this->fetchAll(
            'SELECT * FROM events WHERE user_artist_id = :user_id ORDER BY created_at DESC',
            [':user_id' => $userId]
        );
    }

    public function pendingApproval(): array
    {
        return $this->fetchAll(
            'SELECT events.*, users.full_name AS artist_name FROM events
             LEFT JOIN users ON users.id = events.user_artist_id
             WHERE events.approval_status = :status
             ORDER BY events.created_at DESC',
            [':status' => 'pending']
        );
    }

    public function countPendingApproval(): int
    {
        $row = $this->fetchOne(
            'SELECT COUNT(*) AS count FROM events WHERE approval_status = :status',
            [':status' => 'pending']
        );

        return (int) ($row['count'] ?? 0);
    }

    public function approve(int $id): bool
    {
        return $this->execute(
            'UPDATE events SET approval_status = :status WHERE id = :id',
            [':id' => $id, ':status' => 'approved']
        );
    }

    public function reject(int $id): bool
    {
        return $this->execute(
            'UPDATE events SET approval_status = :status WHERE id = :id',
            [':id' => $id, ':status' => 'rejected']
        );
    }
}

