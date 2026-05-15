<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Artist;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\Notification;
use App\Models\User;

final class AdminController extends Controller
{
    public function dashboard(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            http_response_code(403);
            echo '<h1>403</h1><p>Forbidden.</p>';

            return;
        }

        $stats = [
            'users' => (new User())->countAll(),
            'artists' => (new Artist())->countAll(),
            'events' => (new Event())->countAll(),
            'bookings' => (new Booking())->countAll(),
            'revenue' => (new Booking())->totalRevenue(),
            'pending_events' => (new Event())->countPendingApproval(),
            'pending_messages' => (new ContactMessage())->countPending(),
            'unread_notifications' => (new Notification())->unreadCount((int) ($_SESSION['user']['id'] ?? 0)),
        ];

        $revenueCurve = (new Booking())->revenueByMonth(12);

        $this->render('admin/dashboard', [
            'stats' => $stats,
            'revenueCurve' => $revenueCurve,
        ]);
    }

    public function bookings(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            http_response_code(403);
            echo '<h1>403</h1><p>Forbidden.</p>';

            return;
        }

        $bookings = (new Booking())->allWithDetails();

        $this->render('admin/bookings', [
            'bookings' => $bookings,
        ]);
    }

    public function revenue(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            http_response_code(403);
            echo '<h1>403</h1><p>Forbidden.</p>';

            return;
        }

        $bookingModel = new Booking();

        $this->render('admin/revenue', [
            'totalRevenue' => $bookingModel->totalRevenue(),
            'byCategory' => $bookingModel->revenueByCategory(),
            'byLocation' => $bookingModel->revenueByLocation(),
            'totalBookings' => $bookingModel->countAll(),
            'revenueCurve' => $bookingModel->revenueByMonth(12),
        ]);
    }

    public function liveStats(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        $stats = [
            'users' => (new User())->countAll(),
            'artists' => (new Artist())->countAll(),
            'events' => (new Event())->countAll(),
            'bookings' => (new Booking())->countAll(),
            'revenue' => (float) (new Booking())->totalRevenue(),
            'pending_events' => (new Event())->countPendingApproval(),
            'pending_messages' => (new ContactMessage())->countPending(),
            'unread_notifications' => (new Notification())->unreadCount((int) ($_SESSION['user']['id'] ?? 0)),
            'updated_at' => date('H:i:s'),
        ];

        header('Content-Type: application/json');
        echo json_encode($stats);
    }
}
