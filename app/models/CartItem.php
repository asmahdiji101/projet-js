<?php

declare(strict_types=1);

namespace App\Models;

final class CartItem extends BaseModel
{
    public function byUser(int $userId): array
    {
        return $this->fetchAll(
            'SELECT cart_items.*, tickets.name AS ticket_name, tickets.price, tickets.event_id, tickets.quantity_total, tickets.quantity_sold, events.title AS event_title, events.location, events.image_path AS event_image_path
             FROM cart_items
             INNER JOIN tickets ON tickets.id = cart_items.ticket_id
             INNER JOIN events ON events.id = tickets.event_id
             WHERE cart_items.user_id = :user_id
             ORDER BY cart_items.updated_at DESC',
            [':user_id' => $userId]
        );
    }

    public function findItem(int $userId, int $ticketId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM cart_items WHERE user_id = :user_id AND ticket_id = :ticket_id LIMIT 1',
            [':user_id' => $userId, ':ticket_id' => $ticketId]
        );
    }

    public function add(int $userId, int $ticketId, int $quantity): bool
    {
        $existing = $this->findItem($userId, $ticketId);

        if ($existing !== null) {
            $quantity = max(1, $existing['quantity'] + $quantity);

            return $this->execute(
                'UPDATE cart_items SET quantity = :quantity, updated_at = CURRENT_TIMESTAMP WHERE id = :id',
                [':quantity' => $quantity, ':id' => $existing['id']]
            );
        }

        return $this->execute(
            'INSERT INTO cart_items (user_id, ticket_id, quantity) VALUES (:user_id, :ticket_id, :quantity)',
            [':user_id' => $userId, ':ticket_id' => $ticketId, ':quantity' => $quantity]
        );
    }

    public function update(int $userId, int $ticketId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->remove($userId, $ticketId);
        }

        return $this->execute(
            'UPDATE cart_items SET quantity = :quantity, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id AND ticket_id = :ticket_id',
            [':quantity' => $quantity, ':user_id' => $userId, ':ticket_id' => $ticketId]
        );
    }

    public function remove(int $userId, int $ticketId): bool
    {
        return $this->execute(
            'DELETE FROM cart_items WHERE user_id = :user_id AND ticket_id = :ticket_id',
            [':user_id' => $userId, ':ticket_id' => $ticketId]
        );
    }

    public function clear(int $userId): bool
    {
        return $this->execute(
            'DELETE FROM cart_items WHERE user_id = :user_id',
            [':user_id' => $userId]
        );
    }

    public function mergeSessionCart(int $userId, array $sessionCart): void
    {
        foreach ($sessionCart as $ticketId => $qty) {
            $this->add($userId, (int) $ticketId, (int) $qty);
        }
    }
}
