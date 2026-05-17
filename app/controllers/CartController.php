<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\CartItem;
use App\Models\Event;
use App\Models\Ticket;

final class CartController extends Controller
{
    private function getSessionCart(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    private function getDbCartItems(int $userId): array
    {
        return (new CartItem())->byUser($userId);
    }

    private function getCartForUser(int $userId): array
    {
        $items = $this->getDbCartItems($userId);
        $tickets = [];

        foreach ($items as $item) {
            $item['quantity'] = (int) $item['quantity'];
            $item['line_total'] = (float) $item['price'] * $item['quantity'];
            $tickets[] = $item;
        }

        return $tickets;
    }

    public function index(): void
    {
        $tickets = [];
        $total = 0.0;

        if (isset($_SESSION['user'])) {
            $tickets = $this->getCartForUser((int) $_SESSION['user']['id']);
        } else {
            $cart = $this->getSessionCart();
            $ticketModel = new Ticket();

            foreach ($cart as $ticketId => $qty) {
                $ticket = $ticketModel->findById((int) $ticketId);

                if ($ticket === null) {
                    continue;
                }

                $ticket['quantity'] = (int) $qty;
                $ticket['line_total'] = (float) $ticket['price'] * $ticket['quantity'];
                $tickets[] = $ticket;
            }
        }

        foreach ($tickets as $ticket) {
            $total += $ticket['line_total'] ?? 0.0;
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

        if (!isset($_SESSION['user'])) {
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

        $userId = (int) $_SESSION['user']['id'];
        $existingItem = (new CartItem())->findItem($userId, $ticketId);
        $currentQuantity = $existingItem !== null ? (int) $existingItem['quantity'] : 0;
        $requestedQuantity = $currentQuantity + $quantity;

        if (!$ticketModel->canReserve($ticketId, $requestedQuantity)) {
            $_SESSION['flash_error'] = 'Not enough tickets available.';
            redirect('/cart');
        }

        (new CartItem())->add($userId, $ticketId, $quantity);
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

        if (!isset($_SESSION['user'])) {
            if (isset($_SESSION['cart'][$ticketId])) {
                unset($_SESSION['cart'][$ticketId]);
            }

            redirect('/cart');
        }

        (new CartItem())->remove((int) $_SESSION['user']['id'], $ticketId);
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

        $userId = (int) $_SESSION['user']['id'];
        $cartItems = $this->getDbCartItems($userId);

        if ($cartItems === []) {
            redirect('/cart');
        }

        $ticketModel = new Ticket();
        $bookingModel = new Booking();

        foreach ($cartItems as $item) {
            $ticketId = (int) $item['ticket_id'];
            $qty = (int) $item['quantity'];
            $ticket = $ticketModel->findById($ticketId);

            if ($ticket === null) {
                continue;
            }

            if (!$ticketModel->canReserve($ticketId, $qty)) {
                $_SESSION['flash_error'] = 'Some tickets are no longer available.';
                redirect('/cart');
            }

            $lineTotal = (float) $ticket['price'] * $qty;
            $bookingModel->create($userId, $ticketId, $qty, $lineTotal);
            $ticketModel->increaseSold($ticketId, $qty);
        }

        (new CartItem())->clear($userId);
        unset($_SESSION['cart']);
        $_SESSION['flash_success'] = 'Booking confirmed.';

        redirect('/dashboard');
    }
}
