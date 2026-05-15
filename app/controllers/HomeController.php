<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;

final class HomeController extends Controller
{
    public function index(): void
    {
        $filters = [
            'query' => trim((string) ($_GET['q'] ?? '')),
            'city' => trim((string) ($_GET['city'] ?? '')),
            'date' => trim((string) ($_GET['date'] ?? '')),
            'category' => trim((string) ($_GET['category'] ?? '')),
        ];

        $events = (new Event())->searchPublished($filters);

        $this->render('home/index', [
            'events' => $events,
            'filters' => $filters,
            'categories' => Event::CATEGORIES,
        ]);
    }
}
