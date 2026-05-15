<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Notification;

final class NotificationController extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
            return;
        }

        $userId = (int) $_SESSION['user']['id'];
        $notificationModel = new Notification();
        $notifications = $notificationModel->byUser($userId);

        // Mark all unread notifications as read when user visits the notifications page
        $notificationModel->markAllAsRead($userId);

        $this->render('notification/index', [
            'notifications' => $notifications,
        ]);
    }

    public function open(): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
            return;
        }

        $notificationId = (int) ($_GET['id'] ?? 0);
        $userId = (int) $_SESSION['user']['id'];
        $notificationModel = new Notification();
        $notification = $notificationModel->findByIdAndUser($notificationId, $userId);

        if ($notification === null) {
            redirect('/notifications');
            return;
        }

        $notificationModel->markAsRead($notificationId);

        redirect($notificationModel->targetUrl($notification));
    }
}
