<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;

final class HomeController extends Controller
{
    public function index(): void
    {
        $eventModel = new Event();

        $filters = [
            'query' => trim((string) ($_GET['q'] ?? '')),
            'city' => trim((string) ($_GET['city'] ?? '')),
            'date' => trim((string) ($_GET['date'] ?? '')),
            'category' => trim((string) ($_GET['category'] ?? '')),
        ];

        $events = $eventModel->searchPublished($filters);
        $publishedEvents = $eventModel->allPublished();
        $homeTrends = array_slice($publishedEvents, 0, 3);
        $homePlaces = array_slice(
            array_values(
                array_filter(
                    array_unique(
                        array_map(
                            static fn (array $event): string => (string) ($event['location'] ?? ''),
                            $publishedEvents
                        )
                    )
                )
            ),
            0,
            5
        );

        $this->render('home/index', [
            'events' => $events,
            'filters' => $filters,
            'categories' => Event::CATEGORIES,
            'homeTrends' => $homeTrends,
            'homePlaces' => $homePlaces,
            'layoutSidebar' => false,
        ]);
    }
}
