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

    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM tickets WHERE id = :id LIMIT 1', [':id' => $id]);
    }

    public function canReserve(int $id, int $quantity): bool
    {
        $ticket = $this->findById($id);

        if ($ticket === null) {
            return false;
        }

        $available = (int) $ticket['quantity_total'] - (int) $ticket['quantity_sold'];

        return $available >= $quantity;
    }

    public function increaseSold(int $id, int $quantity): bool
    {
        $sql = 'UPDATE tickets SET quantity_sold = quantity_sold + :qty WHERE id = :id';

        return $this->execute($sql, [':qty' => $quantity, ':id' => $id]);
    }
}
