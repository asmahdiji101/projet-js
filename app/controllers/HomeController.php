<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;

final class HomeController extends Controller
{
    public function index(): void
    {
        $events = (new Event())->allPublished();

        $this->render('home/index', [
            'events' => $events,
        ]);
    }
}
