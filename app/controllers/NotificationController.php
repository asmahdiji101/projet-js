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
}
