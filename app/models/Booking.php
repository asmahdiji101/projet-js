<?php

declare(strict_types=1);

namespace App\Models;

final class Booking extends BaseModel
{
    public function byUser(int $userId): array
    {
        return $this->fetchAll(
            'SELECT bookings.*, tickets.name AS ticket_name, events.title AS event_title
             FROM bookings
             INNER JOIN tickets ON tickets.id = bookings.ticket_id
             INNER JOIN events ON events.id = tickets.event_id
             WHERE bookings.user_id = :user_id
             ORDER BY bookings.created_at DESC',
            [':user_id' => $userId]
        );
    }
}
