<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $driver = strtolower((string) ($_ENV['DB_DRIVER'] ?? 'sqlite'));

        if ($driver === 'mysql') {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $name = $_ENV['DB_NAME'] ?? 'phpjs';
            $user = $_ENV['DB_USER'] ?? 'root';
            $password = $_ENV['DB_PASSWORD'] ?? '';
            $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

            try {
                self::$instance = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $exception) {
                throw new RuntimeException('MySQL connection failed: ' . $exception->getMessage(), 0, $exception);
            }

            return self::$instance;
        }

        // Default to SQLite for local/dev usage when MySQL is not configured
        $sqliteFile = dirname(__DIR__) . '/data/phpjs.sqlite';
        $sqliteDir = dirname($sqliteFile);

        if (!is_dir($sqliteDir)) {
            @mkdir($sqliteDir, 0755, true);
        }

        $dsn = 'sqlite:' . $sqliteFile;

        try {
            self::$instance = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Ensure foreign keys are enforced
            self::$instance->exec('PRAGMA foreign_keys = ON');

            // If this is a new file, create tables
            if (!file_exists($sqliteFile) || filesize($sqliteFile) === 0) {
                self::initializeSqlite(self::$instance);
            }

            self::migrateSqlite(self::$instance);

            $row = self::$instance->query('SELECT COUNT(*) AS count FROM users')->fetch();

            if ((int) ($row['count'] ?? 0) === 0) {
                self::seedSqlite(self::$instance);
            }
        } catch (PDOException $exception) {
            throw new RuntimeException('SQLite connection failed: ' . $exception->getMessage(), 0, $exception);
        }

        return self::$instance;
    }

    private static function initializeSqlite(PDO $pdo): void
    {
        $sql = <<<'SQL'
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user',
            profile_picture_path TEXT DEFAULT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS artists (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER DEFAULT NULL UNIQUE,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT NOT NULL,
            image_path TEXT DEFAULT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            artist_id INTEGER DEFAULT NULL,
            user_artist_id INTEGER DEFAULT NULL,
            category TEXT NOT NULL DEFAULT 'concert',
            title TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT NOT NULL,
            event_date TEXT NOT NULL,
            location TEXT NOT NULL,
            image_path TEXT DEFAULT NULL,
            status TEXT NOT NULL DEFAULT 'draft',
            approval_status TEXT NOT NULL DEFAULT 'pending',
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
            FOREIGN KEY (user_artist_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            price REAL NOT NULL,
            quantity_total INTEGER NOT NULL,
            quantity_sold INTEGER NOT NULL DEFAULT 0,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            ticket_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL,
            total_price REAL NOT NULL,
            status TEXT NOT NULL DEFAULT 'pending',
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS contact_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sender_id INTEGER NOT NULL,
            sender_type TEXT NOT NULL,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'pending',
            admin_reply TEXT DEFAULT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            replied_at TEXT DEFAULT NULL,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS notifications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            type TEXT NOT NULL,
            title TEXT NOT NULL,
            message TEXT NOT NULL,
            related_id INTEGER DEFAULT NULL,
            is_read INTEGER NOT NULL DEFAULT 0,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
        SQL;

        $pdo->exec($sql);
    }

    private static function seedSqlite(PDO $pdo): void
    {
        $pdo->exec("INSERT INTO users (full_name, email, password_hash, role) VALUES
            ('Admin User', 'admin@neonpass.local', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin'),
            ('Demo User', 'user@neonpass.local', '" . password_hash('user123', PASSWORD_DEFAULT) . "', 'user')");

        $pdo->exec("INSERT INTO artists (name, slug, description, image_path) VALUES
            ('Neon Pulse', 'neon-pulse', 'Electronic and live performance collective.', NULL)");

        $artistId = (int) $pdo->lastInsertId();

        $pdo->exec("INSERT INTO events (artist_id, title, slug, description, event_date, location, image_path, status, approval_status) VALUES
            ($artistId, 'Neon Night Live', 'neon-night-live', 'A glowing mixed-media night event.', '2026-05-10 19:30:00', 'Paris Hall 1', NULL, 'published', 'approved')");

        $eventId = (int) $pdo->lastInsertId();

        $pdo->exec("INSERT INTO tickets (event_id, name, price, quantity_total, quantity_sold) VALUES
            ($eventId, 'Standard', 25.00, 120, 0),
            ($eventId, 'VIP', 55.00, 30, 0)");
    }

    private static function migrateSqlite(PDO $pdo): void
    {
        self::ensureColumn($pdo, 'artists', 'user_id', 'INTEGER DEFAULT NULL');
        self::ensureColumn($pdo, 'events', 'category', "TEXT NOT NULL DEFAULT 'concert'");
        self::ensureUniqueIndex($pdo, 'artists', 'user_id');

        $pdo->exec("UPDATE events SET category = 'concert' WHERE category IS NULL OR category = ''");
    }

    private static function ensureColumn(PDO $pdo, string $table, string $column, string $definition): void
    {
        $columns = $pdo->query('PRAGMA table_info(' . $table . ')')->fetchAll();

        foreach ($columns as $existingColumn) {
            if (($existingColumn['name'] ?? null) === $column) {
                return;
            }
        }

        $pdo->exec('ALTER TABLE ' . $table . ' ADD COLUMN ' . $column . ' ' . $definition);
    }

    private static function ensureUniqueIndex(PDO $pdo, string $table, string $column): void
    {
        $indexName = $table . '_' . $column . '_unique_idx';
        $indexes = $pdo->query('PRAGMA index_list(' . $table . ')')->fetchAll();

        foreach ($indexes as $index) {
            if (($index['name'] ?? null) === $indexName) {
                return;
            }
        }

        $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS ' . $indexName . ' ON ' . $table . ' (' . $column . ')');
    }
}
