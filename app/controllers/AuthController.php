<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
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

        $userModel = new User();

        if ($userModel->findByEmail($email) !== null) {
            $this->render('auth/register', [
                'error' => 'Email already exists.',
            ]);

            return;
        }

        $userId = $userModel->create($fullName, $email, password_hash($password, PASSWORD_DEFAULT));

        $_SESSION['user'] = [
            'id' => $userId,
            'full_name' => $fullName,
            'email' => $email,
            'role' => 'user',
        ];

        $redirectTo = $_SESSION['intended'] ?? '/dashboard';
        unset($_SESSION['intended']);

        redirect($redirectTo);
    }

    public function dashboard(): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
        }

        $bookings = (new Booking())->byUser((int) $_SESSION['user']['id']);

        $this->render('auth/dashboard', [
            'user' => $_SESSION['user'],
            'bookings' => $bookings,
        ]);
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        redirect('/');
    }
}
