<?php
require 'config/bootstrap.php';
try {
    $userModel = new \App\Models\User();
    $fullName = 'Test Artist ' . time();
    $email = 'artist' . time() . '@example.com';
    $userId = $userModel->create($fullName, $email, 'hash', 'artist', null);
    
    $artistSlug = trim(strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $fullName)), '-');
    $artistId = (new \App\Models\Artist())->create(
        $fullName,
        $artistSlug,
        'Artist account created from user registration.',
        null,
        $userId
    );
    echo 'SUCCESS_ARTIST:' . $artistId . "\n";
} catch (\Throwable $e) {
    echo 'ERROR:' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n";
}
