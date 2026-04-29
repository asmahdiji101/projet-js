<?php

declare(strict_types=1);

namespace App\Models;

final class Notification extends BaseModel
{
    public function create(int $userId, string $type, string $title, string $message, ?int $relatedId = null): int
    {
        $this->execute(
            'INSERT INTO notifications (user_id, type, title, message, related_id, is_read) VALUES (:user_id, :type, :title, :message, :related_id, :is_read)',
            [
                ':user_id' => $userId,
                ':type' => $type,
                ':title' => $title,
                ':message' => $message,
                ':related_id' => $relatedId,
                ':is_read' => 0,
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function byUser(int $userId): array
    {
        return $this->fetchAll(
            'SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC',
            [':user_id' => $userId]
        );
    }

    public function unreadCount(int $userId): int
    {
        $row = $this->fetchOne(
            'SELECT COUNT(*) AS count FROM notifications WHERE user_id = :user_id AND is_read = 0',
            [':user_id' => $userId]
        );

        return (int) ($row['count'] ?? 0);
    }

    public function markAsRead(int $id): bool
    {
        return $this->execute(
            'UPDATE notifications SET is_read = 1 WHERE id = :id',
            [':id' => $id]
        );
    }
}
