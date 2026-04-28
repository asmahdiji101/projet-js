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
}
