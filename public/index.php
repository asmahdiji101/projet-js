<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ArtistController;
use App\Controllers\CartController;
use App\Controllers\EventController;

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
    case '/events':
        (new EventController())->index();
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
        case '/cart':
            (new CartController())->index();
            break;
        case '/cart/add':
            if ($method === 'POST') {
                (new CartController())->add();
                break;
            }
            http_response_code(405);
            echo 'Method Not Allowed';
            break;
        case '/cart/remove':
            if ($method === 'POST') {
                (new CartController())->remove();
                break;
            }
            http_response_code(405);
            echo 'Method Not Allowed';
            break;
        case '/checkout':
            if ($method === 'POST') {
                (new CartController())->checkout();
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
