<?php

declare(strict_types=1);

namespace App\Models;

final class Ticket extends BaseModel
{
    public function forEvent(int $eventId): array
    {
        return $this->fetchAll(
            'SELECT * FROM tickets WHERE event_id = :event_id ORDER BY price ASC',
            [':event_id' => $eventId]
        );
    }
}
