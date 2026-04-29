<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;
use App\Models\Artist;
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

    public function edit(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $eventId = (int) ($_GET['id'] ?? 0);
        $event = (new Event())->findById($eventId);

        if ($event === null) {
            http_response_code(404);
            echo 'Event not found';
            return;
        }

        $this->render('event/edit', [
            'event' => $event,
            'artists' => (new Artist())->all(),
            'tickets' => (new Ticket())->forEvent($eventId),
        ]);
    }

    public function create(): void
    {
        if (!is_admin() && !is_authenticated()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        // If user is artist, show artist form (no artist selector)
        if (is_authenticated() && $_SESSION['user']['role'] === 'artist') {
            $this->render('event/artist-create');
            return;
        }

        // Admin sees full form
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $artists = (new Artist())->all();

        $this->render('event/create', [
            'artists' => $artists,
        ]);
    }

    public function store(): void
    {
        if (!is_authenticated()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $eventDate = trim($_POST['event_date'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $ticketName = trim($_POST['ticket_name'] ?? 'Standard');
        $ticketPrice = (float) ($_POST['ticket_price'] ?? 0);
        $ticketQuantity = (int) ($_POST['ticket_quantity'] ?? 0);

        $isArtist = $_SESSION['user']['role'] === 'artist';
        $isAdmin = is_admin();

        // Artists can only create events for themselves
        if ($isArtist) {
            $artistId = 0; // Placeholder
            $status = 'draft';
            $approvalStatus = 'pending';
            $userArtistId = (int) $_SESSION['user']['id'];
        } elseif ($isAdmin) {
            $artistId = (int) ($_POST['artist_id'] ?? 0);
            $status = 'published';
            $approvalStatus = 'approved';
            $userArtistId = null;

            if ($artistId <= 0) {
                $this->render('event/create', [
                    'artists' => (new Artist())->all(),
                    'error' => 'Artist is required.',
                ]);
                return;
            }
        } else {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        if ($title === '' || $description === '' || $eventDate === '' || $location === '') {
            if ($isArtist) {
                $this->render('event/artist-create', [
                    'error' => 'All event fields are required.',
                ]);
            } else {
                $this->render('event/create', [
                    'artists' => (new Artist())->all(),
                    'error' => 'All event fields are required.',
                ]);
            }
            return;
        }

        $slug = $this->slugify($title);
        $eventDate = str_replace('T', ' ', $eventDate);
        if (strlen($eventDate) === 16) {
            $eventDate .= ':00';
        }
        $imagePath = null;

        if (!empty($_FILES['image']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $renderView = $isArtist ? 'event/artist-create' : 'event/create';
                $context = $isArtist ? ['error' => 'Invalid image type.'] : ['artists' => (new Artist())->all(), 'error' => 'Invalid image type.'];
                $this->render($renderView, $context);
                return;
            }

            $uploadDir = PUBLIC_PATH . '/uploads/events/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }

            $filename = $slug . '-' . time() . '.' . $ext;
            $target = $uploadDir . $filename;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $renderView = $isArtist ? 'event/artist-create' : 'event/create';
                $context = $isArtist ? ['error' => 'Failed to move file.'] : ['artists' => (new Artist())->all(), 'error' => 'Failed to move file.'];
                $this->render($renderView, $context);
                return;
            }

            $imagePath = '/uploads/events/' . $filename;
        }

        $eventModel = new Event();
        $eventId = $eventModel->create($artistId, $title, $slug, $description, $eventDate, $location, $imagePath, $status, $approvalStatus, $userArtistId);

        if ($ticketName !== '' && $ticketPrice > 0 && $ticketQuantity > 0) {
            (new Ticket())->create($eventId, $ticketName, $ticketPrice, $ticketQuantity);
        }

        if ($isArtist) {
            redirect('/dashboard');
        } else {
            redirect('/events');
        }
    }

    public function update(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $eventId = (int) ($_POST['id'] ?? 0);
        $event = (new Event())->findById($eventId);

        if ($event === null) {
            http_response_code(404);
            echo 'Event not found';
            return;
        }

        $artistId = (int) ($_POST['artist_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $eventDate = trim($_POST['event_date'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $status = trim($_POST['status'] ?? 'published');

        if ($artistId <= 0 || $title === '' || $description === '' || $eventDate === '' || $location === '') {
            $this->render('event/edit', [
                'event' => $event,
                'artists' => (new Artist())->all(),
                'tickets' => (new Ticket())->forEvent($eventId),
                'error' => 'All event fields are required.',
            ]);
            return;
        }

        $slug = $this->slugify($title);
        $eventDate = str_replace('T', ' ', $eventDate);
        if (strlen($eventDate) === 16) {
            $eventDate .= ':00';
        }

        $imagePath = null;

        if (!empty($_FILES['image']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $this->render('event/edit', [
                    'event' => $event,
                    'artists' => (new Artist())->all(),
                    'tickets' => (new Ticket())->forEvent($eventId),
                    'error' => 'Invalid image type.',
                ]);
                return;
            }

            $uploadDir = PUBLIC_PATH . '/uploads/events/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }

            $filename = $slug . '-' . time() . '.' . $ext;
            $target = $uploadDir . $filename;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $this->render('event/edit', [
                    'event' => $event,
                    'artists' => (new Artist())->all(),
                    'tickets' => (new Ticket())->forEvent($eventId),
                    'error' => 'Failed to move uploaded file.',
                ]);
                return;
            }

            $imagePath = '/uploads/events/' . $filename;
        }

        (new Event())->update($eventId, $artistId, $title, $slug, $description, $eventDate, $location, $imagePath, $status);

        redirect('/events/edit?id=' . $eventId);
    }

    public function delete(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $eventId = (int) ($_POST['id'] ?? 0);
        $event = (new Event())->findById($eventId);

        if ($event === null) {
            redirect('/events');
            return;
        }

        if (!empty($event['image_path'])) {
            $file = PUBLIC_PATH . $event['image_path'];
            if (is_file($file)) {
                @unlink($file);
            }
        }

        (new Event())->delete($eventId);

        redirect('/events');
    }

    private function slugify(string $value): string
    {
        $slug = strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', trim($value)));

        return trim($slug, '-');
    }
}