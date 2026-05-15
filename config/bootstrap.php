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

function get_markup_price(float $price): float
{
    return round($price * 1.1, 2);
}

function avatar_url(?string $path = null): string
{
    $defaultAvatar = '/images/default-avatar.svg';

    if ($path === null || $path === '') {
        return $defaultAvatar;
    }

    $normalizedPath = $path;

    if ($normalizedPath[0] !== '/') {
        $normalizedPath = '/' . ltrim($normalizedPath, '/');
    }

    $filesystemPath = PUBLIC_PATH . $normalizedPath;

    if (!is_file($filesystemPath)) {
        return $defaultAvatar;
    }

    return $normalizedPath;
}

function store_uploaded_image(array $file, string $destinationPath, int $maxWidth, int $maxHeight): bool
{
    if (!isset($file['tmp_name']) || !is_string($file['tmp_name']) || $file['tmp_name'] === '' || !is_file($file['tmp_name'])) {
        return false;
    }

    $imageInfo = @getimagesize($file['tmp_name']);

    if ($imageInfo === false) {
        return move_uploaded_file($file['tmp_name'], $destinationPath);
    }

    [$width, $height, $type] = $imageInfo;

    if ($width <= $maxWidth && $height <= $maxHeight) {
        return move_uploaded_file($file['tmp_name'], $destinationPath);
    }

    if (!extension_loaded('gd')) {
        return move_uploaded_file($file['tmp_name'], $destinationPath);
    }

    switch ($type) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($file['tmp_name']);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($file['tmp_name']);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($file['tmp_name']);
            break;
        case IMAGETYPE_WEBP:
            if (!function_exists('imagecreatefromwebp') || !function_exists('imagewebp')) {
                return move_uploaded_file($file['tmp_name'], $destinationPath);
            }

            $sourceImage = imagecreatefromwebp($file['tmp_name']);
            break;
        default:
            return move_uploaded_file($file['tmp_name'], $destinationPath);
    }

    if ($sourceImage === false) {
        return false;
    }

    $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
    $targetWidth = max(1, (int) round($width * $ratio));
    $targetHeight = max(1, (int) round($height * $ratio));
    $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF || $type === IMAGETYPE_WEBP) {
        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);
        $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
        imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);
    }

    imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

    $saved = match ($type) {
        IMAGETYPE_JPEG => imagejpeg($targetImage, $destinationPath, 85),
        IMAGETYPE_PNG => imagepng($targetImage, $destinationPath, 6),
        IMAGETYPE_GIF => imagegif($targetImage, $destinationPath),
        IMAGETYPE_WEBP => imagewebp($targetImage, $destinationPath, 85),
        default => false,
    };

    imagedestroy($sourceImage);
    imagedestroy($targetImage);

    if ($saved) {
        @unlink($file['tmp_name']);
    }

    return $saved;
}

