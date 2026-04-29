<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ContactMessage;

final class ContactController extends Controller
{
    public function show(): void
    {
        // Check if user is logged in
        $isLoggedIn = isset($_SESSION['user']);
        $userRole = $isLoggedIn ? $_SESSION['user']['role'] : 'guest';

        // Route to appropriate form based on role
        if ($userRole === 'artist') {
            $this->showArtistForm();
        } else {
            $this->render('contact/create');
        }
    }

    public function showArtistForm(): void
    {
        $this->render('contact/artist');
    }

    public function store(): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
            return;
        }

        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($subject === '' || $message === '') {
            $this->show();
            return;
        }

        $senderType = $_SESSION['user']['role'] === 'artist' ? 'artist' : 'user';

        (new ContactMessage())->create(
            (int) $_SESSION['user']['id'],
            $senderType,
            $subject,
            $message
        );

        if ($senderType === 'artist') {
            $this->render('contact/artist', ['success' => 'Your message was received by the admin.']);
        } else {
            $this->render('contact/create', ['success' => 'Thanks — your message was received.']);
        }
    }
}
