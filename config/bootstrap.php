<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('VIEW_PATH', APP_PATH . '/views');

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $candidates = [
        APP_PATH . '/' . str_replace('\\', '/', $relativeClass) . '.php',
        CONFIG_PATH . '/' . str_replace('Config\\', '', $relativeClass) . '.php',
    ];

    foreach ($candidates as $file) {
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $viewFile = VIEW_PATH . '/' . $template . '.php';

    if (!is_file($viewFile)) {
        throw new RuntimeException('View not found: ' . $template);
    }

    require VIEW_PATH . '/layouts/main.php';
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_authenticated(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    $user = current_user();

    return ($user['role'] ?? 'user') === 'admin';
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
