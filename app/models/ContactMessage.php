<?php

declare(strict_types=1);

namespace App\Models;

final class ContactMessage extends BaseModel
{
    public function create(int $senderId, string $senderType, string $subject, string $message): int
    {
        $this->execute(
            'INSERT INTO contact_messages (sender_id, sender_type, subject, message, status) VALUES (:sender_id, :sender_type, :subject, :message, :status)',
            [
                ':sender_id' => $senderId,
                ':sender_type' => $senderType,
                ':subject' => $subject,
                ':message' => $message,
                ':status' => 'pending',
            ]
        );

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT cm.*, u.full_name, u.email FROM contact_messages cm
             JOIN users u ON u.id = cm.sender_id
             WHERE cm.id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    public function all(): array
    {
        return $this->fetchAll(
            'SELECT cm.*, u.full_name, u.email FROM contact_messages cm
             JOIN users u ON u.id = cm.sender_id
             ORDER BY cm.created_at DESC'
        );
    }

    public function countPending(): int
    {
        $row = $this->fetchOne(
            'SELECT COUNT(*) AS count FROM contact_messages WHERE status = :status',
            [':status' => 'pending']
        );

        return (int) ($row['count'] ?? 0);
    }

    public function bySender(int $senderId): array
    {
        return $this->fetchAll(
            'SELECT * FROM contact_messages WHERE sender_id = :sender_id ORDER BY created_at DESC',
            [':sender_id' => $senderId]
        );
    }

    public function reply(int $id, string $adminReply): bool
    {
        return $this->execute(
            'UPDATE contact_messages SET admin_reply = :reply, status = :status, replied_at = :replied_at WHERE id = :id',
            [
                ':id' => $id,
                ':reply' => $adminReply,
                ':status' => 'replied',
                ':replied_at' => date('c'),
            ]
        );
    }
}
