<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;
use App\Models\Ticket;

final class TicketController extends Controller
{
    public function create(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $eventId = (int) ($_GET['event_id'] ?? 0);
        $event = (new Event())->findById($eventId);

        if ($event === null) {
            http_response_code(404);
            echo 'Event not found';
            return;
        }

        $this->render('ticket/create', [
            'event' => $event,
        ]);
    }

    public function store(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $eventId = (int) ($_POST['event_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $quantityTotal = (int) ($_POST['quantity_total'] ?? 0);

        $event = (new Event())->findById($eventId);

        if ($event === null) {
            http_response_code(404);
            echo 'Event not found';
            return;
        }

        if ($name === '' || $price <= 0 || $quantityTotal <= 0) {
            $this->render('ticket/create', [
                'event' => $event,
                'error' => 'All ticket fields are required.',
            ]);
            return;
        }

        (new Ticket())->create($eventId, $name, $price, $quantityTotal);

        redirect('/events/edit?id=' . $eventId);
    }

    public function edit(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $ticketId = (int) ($_GET['id'] ?? 0);
        $ticket = (new Ticket())->findById($ticketId);

        if ($ticket === null) {
            http_response_code(404);
            echo 'Ticket not found';
            return;
        }

        $event = (new Event())->findById((int) $ticket['event_id']);

        $this->render('ticket/edit', [
            'ticket' => $ticket,
            'event' => $event,
        ]);
    }

    public function update(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $ticketId = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $quantityTotal = (int) ($_POST['quantity_total'] ?? 0);

        $ticket = (new Ticket())->findById($ticketId);

        if ($ticket === null) {
            http_response_code(404);
            echo 'Ticket not found';
            return;
        }

        $sold = (int) $ticket['quantity_sold'];

        if ($name === '' || $price <= 0 || $quantityTotal < $sold) {
            $this->render('ticket/edit', [
                'ticket' => $ticket,
                'event' => (new Event())->findById((int) $ticket['event_id']),
                'error' => 'Quantity must stay above sold tickets and fields must be valid.',
            ]);
            return;
        }

        (new Ticket())->update($ticketId, $name, $price, $quantityTotal);

        redirect('/events/edit?id=' . (int) $ticket['event_id']);
    }

    public function delete(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $ticketId = (int) ($_POST['id'] ?? 0);
        $ticket = (new Ticket())->findById($ticketId);

        if ($ticket === null) {
            redirect('/events');
            return;
        }

        if ((int) $ticket['quantity_sold'] > 0) {
            $_SESSION['flash_error'] = 'This ticket has bookings and cannot be deleted.';
            redirect('/events/edit?id=' . (int) $ticket['event_id']);
        }

        (new Ticket())->delete($ticketId);

        redirect('/events/edit?id=' . (int) $ticket['event_id']);
    }
}
