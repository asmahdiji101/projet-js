<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class HomeController extends Controller
{
    public function index(): void
    {
        $featuredEvents = [
            [
                'title' => 'Neon Night Live',
                'subtitle' => 'Festival urbain, scène DJ et performances visuelles',
                'price' => 'From 25€',
            ],
            [
                'title' => 'Skyline Culture Pass',
                'subtitle' => 'Expériences premium, ateliers et accès VIP',
                'price' => 'From 18€',
            ],
        ];

        $this->render('home/index', [
            'featuredEvents' => $featuredEvents,
        ]);
    }
}
