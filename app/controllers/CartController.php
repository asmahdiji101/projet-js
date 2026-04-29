<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Ticket;

final class CartController extends Controller
{
    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $tickets = [];
        $total = 0.0;
        $ticketModel = new Ticket();

        foreach ($cart as $ticketId => $qty) {
            $ticket = $ticketModel->findById((int) $ticketId);

            if ($ticket === null) {
                continue;
            }

            $ticket['quantity'] = (int) $qty;
            $ticket['line_total'] = (float) $ticket['price'] * (int) $qty;
            $total += $ticket['line_total'];
            $tickets[] = $ticket;
        }

        $this->render('cart/index', [
            'tickets' => $tickets,
            'total' => $total,
        ]);
    }

    public function add(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        $ticketId = (int) ($_POST['ticket_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        if ($ticketId <= 0) {
            redirect('/events');
        }

        $ticketModel = new Ticket();
        $currentQuantity = (int) ($_SESSION['cart'][$ticketId] ?? 0);
        $requestedQuantity = $currentQuantity + $quantity;

        if (!$ticketModel->canReserve($ticketId, $requestedQuantity)) {
            $_SESSION['flash_error'] = 'Not enough tickets available.';
            redirect('/cart');
        }

        $_SESSION['cart'] ??= [];
        $_SESSION['cart'][$ticketId] = ($_SESSION['cart'][$ticketId] ?? 0) + $quantity;

        redirect('/cart');
    }

    public function remove(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        $ticketId = (int) ($_POST['ticket_id'] ?? 0);

        if (isset($_SESSION['cart'][$ticketId])) {
            unset($_SESSION['cart'][$ticketId]);
        }

        redirect('/cart');
    }

    public function checkout(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        if (!isset($_SESSION['user'])) {
            $_SESSION['intended'] = '/cart';
            redirect('/login');
        }

        $cart = $_SESSION['cart'] ?? [];

        if ($cart === []) {
            redirect('/cart');
        }

        $ticketModel = new Ticket();
        $bookingModel = new Booking();
        $userId = (int) $_SESSION['user']['id'];

        foreach ($cart as $ticketId => $qty) {
            $ticket = $ticketModel->findById((int) $ticketId);

            if ($ticket === null) {
                continue;
            }

            if (!$ticketModel->canReserve((int) $ticketId, (int) $qty)) {
                $_SESSION['flash_error'] = 'Some tickets are no longer available.';
                redirect('/cart');
            }

            $lineTotal = (float) $ticket['price'] * (int) $qty;

            $bookingModel->create($userId, (int) $ticketId, (int) $qty, $lineTotal);
            $ticketModel->increaseSold((int) $ticketId, (int) $qty);
        }

        unset($_SESSION['cart']);
        $_SESSION['flash_success'] = 'Booking confirmed.';

        redirect('/dashboard');
    }
}
