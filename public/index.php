<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ArtistController;

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = rtrim($uri, '/') ?: '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'POST' && $uri === '/login') {
    (new AuthController())->login();
    return;
}

if ($method === 'POST' && $uri === '/register') {
    (new AuthController())->register();
    return;
}

switch ($uri) {
    case '/':
        (new HomeController())->index();
        break;
    case '/login':
        (new AuthController())->showLogin();
        break;
    case '/register':
        (new AuthController())->showRegister();
        break;
    case '/dashboard':
        (new AuthController())->dashboard();
        break;
    case '/admin':
        (new AdminController())->dashboard();
        break;
        case '/artists':
            (new ArtistController())->index();
            break;
        case '/artists/create':
            (new ArtistController())->create();
            break;
        case '/artists/store':
            if ($method === 'POST') {
                (new ArtistController())->store();
                break;
            }
            http_response_code(405);
            echo 'Method Not Allowed';
            break;
    case '/logout':
        (new AuthController())->logout();
        break;
    default:
        http_response_code(404);
        echo '<h1>404</h1><p>Page not found.</p>';
        break;
}
