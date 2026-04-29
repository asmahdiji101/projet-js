<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\Notification;

final class AdminMessagingController extends Controller
{
    public function messages(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $messages = (new ContactMessage())->all();

        $this->render('admin/messages', [
            'messages' => $messages,
        ]);
    }

    public function showMessage(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $id = (int) ($_GET['id'] ?? 0);
        $message = (new ContactMessage())->findById($id);

        if ($message === null) {
            http_response_code(404);
            echo 'Message not found';
            return;
        }

        $this->render('admin/message-view', [
            'message' => $message,
        ]);
    }

    public function reply(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $reply = trim($_POST['reply'] ?? '');

        if ($reply === '') {
            $message = (new ContactMessage())->findById($id);
            $this->render('admin/message-view', [
                'message' => $message,
                'error' => 'Reply cannot be empty.',
            ]);
            return;
        }

        $message = (new ContactMessage())->findById($id);

        if ($message === null) {
            redirect('/admin/messages');
            return;
        }

        (new ContactMessage())->reply($id, $reply);

        // Send notification to sender
        (new Notification())->create(
            (int) $message['sender_id'],
            'message_reply',
            'Admin replied to your message',
            substr($reply, 0, 100) . '...',
            $id
        );

        redirect('/admin/messages');
    }

    public function pendingEvents(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $events = (new Event())->pendingApproval();

        $this->render('admin/pending-events', [
            'events' => $events,
        ]);
    }

    public function approveEvent(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $event = (new Event())->findById($id);

        if ($event === null) {
            redirect('/admin/pending-events');
            return;
        }

        (new Event())->approve($id);
        (new Event())->update(
            $id,
            (int) $event['artist_id'],
            $event['title'],
            $event['slug'],
            $event['description'],
            $event['event_date'],
            $event['location'],
            null,
            'published'
        );

        // Send notification to artist if they created it
        if (!empty($event['user_artist_id'])) {
            (new Notification())->create(
                (int) $event['user_artist_id'],
                'event_approved',
                'Your event was approved!',
                'Event "' . $event['title'] . '" is now live. Prices have 10% markup applied.',
                $id
            );
        }

        redirect('/admin/pending-events');
    }

    public function rejectEvent(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $event = (new Event())->findById($id);

        if ($event === null) {
            redirect('/admin/pending-events');
            return;
        }

        (new Event())->reject($id);

        // Send notification to artist if they created it
        if (!empty($event['user_artist_id'])) {
            (new Notification())->create(
                (int) $event['user_artist_id'],
                'event_rejected',
                'Your event was rejected',
                'Event "' . $event['title'] . '" was not approved.',
                $id
            );
        }

        redirect('/admin/pending-events');
    }
}
