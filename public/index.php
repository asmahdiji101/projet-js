<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use App\Controllers\ArtistController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\EventController;
use App\Controllers\HomeController;
use App\Controllers\TicketController;
use App\Controllers\ContactController;
use App\Controllers\AdminMessagingController;

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = rtrim($uri, '/') ?: '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Dynamic route: event detail /events/{id}
if ($method === 'GET' && preg_match('#^/events/(\d+)$#', $uri, $m)) {
    (new EventController())->show((int)$m[1]);
    return;
}

if ($method === 'POST' && $uri === '/login') {
    (new AuthController())->login();
    return;
}

if ($method === 'POST' && $uri === '/register') {
    (new AuthController())->register();
    return;
}

if ($method === 'GET' && $uri === '/account/edit') {
    (new AuthController())->showEditProfile();
    return;
}

if ($method === 'POST' && $uri === '/account/update') {
    (new AuthController())->updateProfile();
    return;
}

if ($method === 'POST' && $uri === '/events/store') {
    (new EventController())->store();
    return;
}

if ($method === 'POST' && $uri === '/events/update') {
    (new EventController())->update();
    return;
}

if ($method === 'POST' && $uri === '/events/delete') {
    (new EventController())->delete();
    return;
}

if ($method === 'POST' && $uri === '/artists/store') {
    (new ArtistController())->store();
    return;
}
 
if ($method === 'POST' && $uri === '/artists/update') {
    (new ArtistController())->update();
    return;
}

if ($method === 'POST' && $uri === '/artists/delete') {
    (new ArtistController())->delete();
    return;
}

if ($method === 'POST' && $uri === '/tickets/store') {
    (new TicketController())->store();
    return;
}

if ($method === 'POST' && $uri === '/tickets/update') {
    (new TicketController())->update();
    return;
}

if ($method === 'POST' && $uri === '/tickets/delete') {
    (new TicketController())->delete();
    return;
}

if ($method === 'POST' && $uri === '/cart/add') {
    (new CartController())->add();
    return;
}

if ($method === 'POST' && $uri === '/cart/remove') {
    (new CartController())->remove();
    return;
}

if ($method === 'POST' && $uri === '/checkout') {
    (new CartController())->checkout();
    return;
}

if ($method === 'POST' && $uri === '/contact/store') {
    (new ContactController())->store();
    return;
}

if ($method === 'POST' && $uri === '/admin/message/reply') {
    (new AdminMessagingController())->reply();
    return;
}

if ($method === 'POST' && $uri === '/admin/event/approve') {
    (new AdminMessagingController())->approveEvent();
    return;
}

if ($method === 'POST' && $uri === '/admin/event/reject') {
    (new AdminMessagingController())->rejectEvent();
    return;
}

if ($method === 'GET' && $uri === '/notifications') {
    (new App\Controllers\NotificationController())->index();
    return;
}

if ($method === 'GET' && $uri === '/notifications/open') {
    (new App\Controllers\NotificationController())->open();
    return;
}

if ($method === 'GET' && $uri === '/admin/stats/live') {
    (new AdminController())->liveStats();
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
    case '/bookings':
        (new AuthController())->bookings();
        break;
    case '/admin':
        (new AdminController())->dashboard();
        break;
    case '/admin/bookings':
        (new AdminController())->bookings();
        break;
    case '/admin/revenue':
        (new AdminController())->revenue();
        break;
    case '/events':
        (new EventController())->index();
        break;
    case '/events/create':
        (new EventController())->create();
        break;
    case '/events/edit':
        (new EventController())->edit();
        break;
    case '/events/artist-events':
        (new EventController())->artistEvents();
        break;
    case '/artists':
        (new ArtistController())->index();
        break;
    case '/artists/create':
        (new ArtistController())->create();
        break;
    case '/artists/edit':
        (new ArtistController())->edit();
        break;
    case '/cart':
        (new CartController())->index();
        break;
    case '/contact':
        (new ContactController())->show();
        break;
    case '/admin/messages':
        (new AdminMessagingController())->messages();
        break;
    case '/admin/message':
        (new AdminMessagingController())->showMessage();
        break;
    case '/admin/pending-events':
        (new AdminMessagingController())->pendingEvents();
        break;
    case '/tickets/create':
        (new TicketController())->create();
        break;
    case '/tickets/edit':
        (new TicketController())->edit();
        break;
    case '/logout':
        (new AuthController())->logout();
        break;
    default:
        http_response_code(404);
        echo '<h1>404</h1><p>Page not found.</p>';
        break;
}
