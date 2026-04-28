<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

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
            'users' => 0,
            'artists' => 0,
            'events' => 0,
            'bookings' => 0,
        ];

        $this->render('admin/dashboard', [
            'stats' => $stats,
        ]);
    }
}
