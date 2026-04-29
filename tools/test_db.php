<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    $path = __DIR__ . '/../data/phpjs.sqlite';
    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "DB-OK\n";
} catch (Throwable $e) {
    echo "DB-ERR: " . $e->getMessage() . "\n";
}
