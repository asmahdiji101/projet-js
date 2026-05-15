<?php

declare(strict_types=1);

namespace App\Models;

final class Event extends BaseModel
{
    public const CATEGORIES = [
        'concert' => 'Concerts',
        'excursion' => 'Excursions',
        'festival' => 'Festivals',
        'food' => 'Food',
        'humanitaire' => 'Humanitaires',
        'loisir' => 'Loisirs',
        'professionnel' => 'Professionnels',
    ];

    public function allPublished(): array
    {
        return $this->searchPublished([]);
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

    public function create(?int $artistId, string $title, string $slug, string $description, string $eventDate, string $location, ?string $imagePath, string $status = 'published', string $approvalStatus = 'approved', ?int $userArtistId = null, string $category = 'concert'): int
    {
        $this->execute(
            'INSERT INTO events (artist_id, user_artist_id, category, title, slug, description, event_date, location, image_path, status, approval_status) VALUES (:artist_id, :user_artist_id, :category, :title, :slug, :description, :event_date, :location, :image_path, :status, :approval_status)',
            [
                ':artist_id' => $artistId,
                ':user_artist_id' => $userArtistId,
                ':category' => $category,
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

    public function update(int $id, ?int $artistId, string $title, string $slug, string $description, string $eventDate, string $location, ?string $imagePath, string $status, string $category = 'concert'): bool
    {
        return $this->execute(
            'UPDATE events SET artist_id = :artist_id, category = :category, title = :title, slug = :slug, description = :description, event_date = :event_date, location = :location, image_path = COALESCE(:image_path, image_path), status = :status WHERE id = :id',
            [
                ':id' => $id,
                ':artist_id' => $artistId,
                ':category' => $category,
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

    public function searchPublished(array $filters = []): array
    {
        $sql = 'SELECT events.*, COALESCE(artists.name, users.full_name, "Artist") AS artist_name
                FROM events
                LEFT JOIN artists ON artists.id = events.artist_id
                LEFT JOIN users ON users.id = events.user_artist_id
                WHERE events.status = :status AND events.approval_status = :approval';
        $params = [
            ':status' => 'published',
            ':approval' => 'approved',
        ];

        $query = trim((string) ($filters['query'] ?? ''));
        $city = trim((string) ($filters['city'] ?? ''));
        $date = trim((string) ($filters['date'] ?? ''));
        $category = trim((string) ($filters['category'] ?? ''));

        if ($query !== '') {
            $sql .= ' AND (events.title LIKE :query OR events.description LIKE :query OR events.location LIKE :query OR COALESCE(artists.name, users.full_name, "Artist") LIKE :query)';
            $params[':query'] = '%' . $query . '%';
        }

        if ($city !== '') {
            $sql .= ' AND events.location LIKE :city';
            $params[':city'] = '%' . $city . '%';
        }

        if ($date !== '') {
            $sql .= ' AND DATE(events.event_date) = :date';
            $params[':date'] = $date;
        }

        if ($category !== '' && isset(self::CATEGORIES[$category])) {
            $sql .= ' AND events.category = :category';
            $params[':category'] = $category;
        }

        $sql .= ' ORDER BY events.event_date ASC';

        return $this->fetchAll($sql, $params);
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

