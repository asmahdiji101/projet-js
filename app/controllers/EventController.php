<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;
use App\Models\Ticket;

final class EventController extends Controller
{
    public function index(): void
    {
        $events = (new Event())->allPublished();

        foreach ($events as &$event) {
            $event['tickets'] = (new Ticket())->forEvent((int) $event['id']);
        }

        $this->render('event/index', [
            'events' => $events,
        ]);
    }
}