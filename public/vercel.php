<?php

// Enable error reporting for deployment debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration for Vercel
$database_path = '/tmp/database.sqlite';
$storage_path = '/tmp/storage';

// Initialize database if it doesn't exist
if (!file_exists($database_path)) {
    touch($database_path);
    chmod($database_path, 0777);

    // Connect to SQLite and enable foreign keys
    if (extension_loaded('pdo_sqlite')) {
        try {
            $pdo = new PDO('sqlite:' . $database_path);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec('PRAGMA foreign_keys = ON;');
            
            // Create migrations table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS migrations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    migration VARCHAR NOT NULL,
                    batch INTEGER NOT NULL
                );
                CREATE TABLE IF NOT EXISTS failed_jobs (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    uuid VARCHAR NOT NULL UNIQUE,
                    connection TEXT NOT NULL,
                    queue TEXT NOT NULL,
                    payload TEXT NOT NULL,
                    exception TEXT NOT NULL,
                    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            ");
            echo "Database initialized successfully\n";
        } catch (Exception $e) {
            echo "Database initialization error: " . $e->getMessage() . "\n";
        }
    }
}

// Create required directories
$storage_dirs = [
    $storage_path . '/framework/cache',
    $storage_path . '/framework/sessions',
    $storage_path . '/framework/views',
    $storage_path . '/logs',
    $storage_path . '/app/public'
];

foreach ($storage_dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    chmod($dir, 0777);
}

// Set storage directory symlink
$public_storage = __DIR__ . '/storage';
if (!file_exists($public_storage)) {
    @symlink($storage_path, $public_storage);
}

// Continue with normal application bootstrapping
require __DIR__ . '/index.php';