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

    public function countAll(): int
    {
        $row = $this->fetchOne('SELECT COUNT(*) AS count FROM bookings');

        return (int) ($row['count'] ?? 0);
    }

    public function totalRevenue(): float
    {
        $row = $this->fetchOne("SELECT COALESCE(SUM(total_price), 0) AS revenue FROM bookings WHERE status = 'confirmed'");

        return (float) ($row['revenue'] ?? 0);
    }

    public function create(int $userId, int $ticketId, int $quantity, float $totalPrice): int
    {
        $this->execute(
            'INSERT INTO bookings (user_id, ticket_id, quantity, total_price, status) VALUES (:user_id, :ticket_id, :quantity, :total_price, :status)',
            [
                ':user_id' => $userId,
                ':ticket_id' => $ticketId,
                ':quantity' => $quantity,
                ':total_price' => $totalPrice,
                ':status' => 'confirmed',
            ]
        );

        return (int) $this->db->lastInsertId();
    }
}
