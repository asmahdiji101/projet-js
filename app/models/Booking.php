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

    public function allWithDetails(): array
    {
        return $this->fetchAll(
            'SELECT bookings.*, users.full_name AS user_name, users.email AS user_email, tickets.name AS ticket_name, events.title AS event_title, events.category AS event_category, events.location AS event_location, events.event_date AS event_date
             FROM bookings
             INNER JOIN users ON users.id = bookings.user_id
             INNER JOIN tickets ON tickets.id = bookings.ticket_id
             INNER JOIN events ON events.id = tickets.event_id
             ORDER BY bookings.created_at DESC'
        );
    }

    public function revenueByCategory(): array
    {
        return $this->fetchAll(
            'SELECT COALESCE(events.category, "unknown") AS category, COUNT(*) AS bookings_count, COALESCE(SUM(bookings.total_price), 0) AS revenue
             FROM bookings
             INNER JOIN tickets ON tickets.id = bookings.ticket_id
             INNER JOIN events ON events.id = tickets.event_id
             WHERE bookings.status = :status
             GROUP BY COALESCE(events.category, "unknown")
             ORDER BY revenue DESC',
            [':status' => 'confirmed']
        );
    }

    public function revenueByLocation(): array
    {
        return $this->fetchAll(
            'SELECT COALESCE(events.location, "unknown") AS location, COUNT(*) AS bookings_count, COALESCE(SUM(bookings.total_price), 0) AS revenue
             FROM bookings
             INNER JOIN tickets ON tickets.id = bookings.ticket_id
             INNER JOIN events ON events.id = tickets.event_id
             WHERE bookings.status = :status
             GROUP BY COALESCE(events.location, "unknown")
             ORDER BY revenue DESC',
            [':status' => 'confirmed']
        );
    }

    public function revenueByMonth(int $months = 12): array
    {
        $rows = $this->fetchAll(
            'SELECT strftime("%Y-%m", bookings.created_at) AS month_key, COALESCE(SUM(bookings.total_price), 0) AS revenue
             FROM bookings
             WHERE bookings.status = :status
             GROUP BY strftime("%Y-%m", bookings.created_at)
             ORDER BY month_key ASC',
            [':status' => 'confirmed']
        );

        $rowMap = [];
        foreach ($rows as $row) {
            $rowMap[(string) $row['month_key']] = (float) $row['revenue'];
        }

        $labels = [];
        $values = [];
        $current = new \DateTimeImmutable('first day of this month');
        $start = $current->modify('-' . max(0, $months - 1) . ' months');

        for ($index = 0; $index < $months; $index++) {
            $month = $start->modify('+' . $index . ' months');
            $key = $month->format('Y-m');
            $labels[] = $month->format('M');
            $values[] = $rowMap[$key] ?? 0.0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
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
