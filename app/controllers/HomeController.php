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

        // Show up to 3 featured events on the home page
        $featured = array_slice($events, 0, 3);

        $this->render('home/index', [
            'featuredEvents' => $featured,
        ]);
    }
}
