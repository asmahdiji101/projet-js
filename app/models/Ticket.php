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

    public function allForEvent(int $eventId): array
    {
        return $this->forEvent($eventId);
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

    public function create(int $eventId, string $name, float $price, int $quantityTotal): int
    {
        $this->execute(
            'INSERT INTO tickets (event_id, name, price, quantity_total, quantity_sold) VALUES (:event_id, :name, :price, :quantity_total, 0)',
            [
                ':event_id' => $eventId,
                ':name' => $name,
                ':price' => $price,
                ':quantity_total' => $quantityTotal,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, float $price, int $quantityTotal): bool
    {
        return $this->execute(
            'UPDATE tickets SET name = :name, price = :price, quantity_total = :quantity_total WHERE id = :id',
            [
                ':id' => $id,
                ':name' => $name,
                ':price' => $price,
                ':quantity_total' => $quantityTotal,
            ]
        );
    }

    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM tickets WHERE id = :id', [':id' => $id]);
    }

    public function getMarkupPrice(float $originalPrice): float
    {
        return round($originalPrice * 1.1, 2);
    }

    public function forEventWithMarkup(int $eventId, bool $isApproved = false): array
    {
        $tickets = $this->forEvent($eventId);

        if ($isApproved) {
            foreach ($tickets as &$ticket) {
                $ticket['published_price'] = $this->getMarkupPrice((float) $ticket['price']);
            }
        }

        return $tickets;
    }
}
