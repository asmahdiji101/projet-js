<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Artist;
use App\Models\User;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->render('auth/login');
    }

    public function showRegister(): void
    {
        $this->render('auth/register');
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $user = (new User())->findByEmail($email);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            $this->render('auth/login', [
                'error' => 'Invalid email or password.',
            ]);

            return;
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'profile_picture_path' => $user['profile_picture_path'] ?? null,
        ];

        $redirectTo = $_SESSION['intended'] ?? '/dashboard';
        unset($_SESSION['intended']);

        redirect($redirectTo);
    }

    public function register(): void
    {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
        $accountType = trim($_POST['account_type'] ?? 'participant');

        if ($fullName === '' || $email === '' || $password === '') {
            $this->render('auth/register', [
                'error' => 'All fields are required.',
            ]);

            return;
        }

        if ($password !== $confirmPassword) {
            $this->render('auth/register', [
                'error' => 'Passwords do not match.',
            ]);

            return;
        }

        $role = ($accountType === 'artist') ? 'artist' : 'user';

        // Handle profile picture upload
        $profilePicturePath = null;
        $uploadDir = PUBLIC_PATH . '/uploads/profiles/';

        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        // Profile picture is required for artists, optional for participants
        $isArtist = ($role === 'artist');
        $hasProfilePictureFile = isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK;

        if ($isArtist && !$hasProfilePictureFile) {
            $this->render('auth/register', [
                'error' => 'Profile picture is required for artists.',
            ]);
            return;
        }

        if ($hasProfilePictureFile) {
            $file = $_FILES['profile_picture'];
            // Limit profile picture to 1MB
            if (isset($file['size']) && $file['size'] > 1 * 1024 * 1024) {
                $this->render('auth/register', [
                    'error' => 'Profile picture must be <= 1MB.',
                ]);
                return;
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($ext, $allowedExts, true)) {
                $this->render('auth/register', [
                    'error' => 'Only image files (JPG, PNG, GIF, WebP) are allowed.',
                ]);
                return;
            }

            $filename = uniqid(str_replace(' ', '-', strtolower($fullName)) . '-', true) . '.' . $ext;
            $target = $uploadDir . $filename;

            if (!store_uploaded_image($file, $target, 300, 300)) {
                $this->render('auth/register', [
                    'error' => 'Failed to upload profile picture.',
                ]);
                return;
            }

            $profilePicturePath = '/uploads/profiles/' . $filename;
        }

        $userModel = new User();

        if ($userModel->findByEmail($email) !== null) {
            $this->render('auth/register', [
                'error' => 'Email already exists.',
            ]);

            return;
        }

        $role = ($accountType === 'artist') ? 'artist' : 'user';

        $userId = $userModel->create($fullName, $email, password_hash($password, PASSWORD_DEFAULT), $role, $profilePicturePath);

        if ($role === 'artist') {
            $artistSlug = trim(strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $fullName)), '-');
            (new Artist())->create(
                $fullName,
                $artistSlug,
                'Artist account created from user registration.',
                $profilePicturePath,
                $userId
            );
        }

        $_SESSION['user'] = [
            'id' => $userId,
            'full_name' => $fullName,
            'email' => $email,
            'role' => $role,
            'profile_picture_path' => $profilePicturePath,
        ];

        $redirectTo = $_SESSION['intended'] ?? '/dashboard';
        unset($_SESSION['intended']);

        redirect($redirectTo);
    }

    public function showEditProfile(): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
        }

        $user = (new User())->findById((int) $_SESSION['user']['id']);

        if ($user === null) {
            redirect('/logout');
        }

        $artist = null;
        if (($user['role'] ?? 'user') === 'artist') {
            $artist = (new Artist())->findByUserId((int) $user['id']);
        }

        $this->render('auth/edit', [
            'user' => $user,
            'artist' => $artist,
        ]);
    }

    public function updateProfile(): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
        }

        $currentUser = (new User())->findById((int) $_SESSION['user']['id']);

        if ($currentUser === null) {
            redirect('/logout');
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($fullName === '' || $email === '') {
            $artist = $currentUser['role'] === 'artist' ? (new Artist())->findByUserId((int) $currentUser['id']) : null;
            $this->render('auth/edit', [
                'user' => $currentUser,
                'artist' => $artist,
                'error' => 'Name and email are required.',
            ]);

            return;
        }

        $existing = (new User())->findByEmail($email);
        if ($existing !== null && (int) $existing['id'] !== (int) $currentUser['id']) {
            $artist = $currentUser['role'] === 'artist' ? (new Artist())->findByUserId((int) $currentUser['id']) : null;
            $this->render('auth/edit', [
                'user' => $currentUser,
                'artist' => $artist,
                'error' => 'Email already exists.',
            ]);

            return;
        }

        $profilePicturePath = $currentUser['profile_picture_path'] ?? null;
        $hasProfilePictureFile = isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK;

        if ($hasProfilePictureFile) {
            $file = $_FILES['profile_picture'];

            if (isset($file['size']) && $file['size'] > 1 * 1024 * 1024) {
                $artist = $currentUser['role'] === 'artist' ? (new Artist())->findByUserId((int) $currentUser['id']) : null;
                $this->render('auth/edit', [
                    'user' => $currentUser,
                    'artist' => $artist,
                    'error' => 'Profile picture must be <= 1MB.',
                ]);

                return;
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($ext, $allowedExts, true)) {
                $artist = $currentUser['role'] === 'artist' ? (new Artist())->findByUserId((int) $currentUser['id']) : null;
                $this->render('auth/edit', [
                    'user' => $currentUser,
                    'artist' => $artist,
                    'error' => 'Only image files (JPG, PNG, GIF, WebP) are allowed.',
                ]);

                return;
            }

            $uploadDir = PUBLIC_PATH . '/uploads/profiles/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }

            $filename = uniqid(str_replace(' ', '-', strtolower($fullName)) . '-', true) . '.' . $ext;
            $target = $uploadDir . $filename;

            if (!store_uploaded_image($file, $target, 300, 300)) {
                $artist = $currentUser['role'] === 'artist' ? (new Artist())->findByUserId((int) $currentUser['id']) : null;
                $this->render('auth/edit', [
                    'user' => $currentUser,
                    'artist' => $artist,
                    'error' => 'Failed to upload profile picture.',
                ]);

                return;
            }

            $profilePicturePath = '/uploads/profiles/' . $filename;
        }

        $passwordHash = null;
        if ($newPassword !== '' || $confirmPassword !== '') {
            if ($newPassword === '' || $newPassword !== $confirmPassword) {
                $artist = $currentUser['role'] === 'artist' ? (new Artist())->findByUserId((int) $currentUser['id']) : null;
                $this->render('auth/edit', [
                    'user' => $currentUser,
                    'artist' => $artist,
                    'error' => 'Passwords do not match.',
                ]);

                return;
            }

            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        (new User())->updateProfile((int) $currentUser['id'], $fullName, $email, $passwordHash, $profilePicturePath);

        if (($currentUser['role'] ?? 'user') === 'artist') {
            $artistModel = new Artist();
            $artist = $artistModel->findByUserId((int) $currentUser['id']);
            $artistSlug = trim(strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $fullName)), '-');

            if ($artist === null) {
                $artistModel->create($fullName, $artistSlug, 'Artist account created from user registration.', $profilePicturePath, (int) $currentUser['id']);
            } else {
                $artistModel->update(
                    (int) $artist['id'],
                    $fullName,
                    $artistSlug,
                    (string) ($artist['description'] ?? 'Artist account created from user registration.'),
                    $profilePicturePath,
                    (int) $currentUser['id']
                );
            }
        }

        $_SESSION['user']['full_name'] = $fullName;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['profile_picture_path'] = $profilePicturePath;

        redirect('/dashboard');
    }

    public function dashboard(): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
        }

        $bookings = (new Booking())->byUser((int) $_SESSION['user']['id']);
        $notifications = [];
        $artistEvents = [];

        // Get notifications
        $notificationModel = new \App\Models\Notification();
        $notifications = $notificationModel->byUser((int) $_SESSION['user']['id']);

        // If artist, show their events
        if ($_SESSION['user']['role'] === 'artist') {
            $eventModel = new \App\Models\Event();
            $artistEvents = $eventModel->byArtistUser((int) $_SESSION['user']['id']);
        }

        $this->render('auth/dashboard', [
            'user' => $_SESSION['user'],
            'bookings' => $bookings,
            'notifications' => $notifications,
            'artistEvents' => $artistEvents,
        ]);
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        redirect('/');
    }
}
