<?php

declare(strict_types=1);

namespace App\Models;

final class Event extends BaseModel
{
    public function allPublished(): array
    {
        return $this->fetchAll(
            'SELECT events.*, artists.name AS artist_name
             FROM events
             INNER JOIN artists ON artists.id = events.artist_id
             WHERE events.status = :status
             ORDER BY events.event_date ASC',
            [':status' => 'published']
        );
    }

    public function findPublishedById(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT events.*, artists.name AS artist_name
             FROM events
             INNER JOIN artists ON artists.id = events.artist_id
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
}
