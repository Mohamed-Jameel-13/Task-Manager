<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up base paths
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../public';
$_SERVER['APP_BASE_PATH'] = dirname(__DIR__);
chdir($_SERVER['DOCUMENT_ROOT']);

// Create required directories
$storage_path = '/tmp/storage';
$bootstrap_path = '/tmp/bootstrap';

// Create storage directories if they don't exist
if (!file_exists($storage_path)) {
    mkdir($storage_path, 0777, true);
    mkdir($storage_path . '/app', 0777, true);
    mkdir($storage_path . '/app/public', 0777, true);
    mkdir($storage_path . '/framework', 0777, true);
    mkdir($storage_path . '/framework/cache', 0777, true);
    mkdir($storage_path . '/framework/sessions', 0777, true);
    mkdir($storage_path . '/framework/views', 0777, true);
    mkdir($storage_path . '/logs', 0777, true);
}

// Create bootstrap cache directory
if (!file_exists($bootstrap_path)) {
    mkdir($bootstrap_path, 0777, true);
    mkdir($bootstrap_path . '/cache', 0777, true);
}

// Create SQLite database if it doesn't exist
$database_path = '/tmp/database.sqlite';
if (!file_exists($database_path)) {
    touch($database_path);
    chmod($database_path, 0777);

    if (extension_loaded('pdo_sqlite')) {
        try {
            $pdo = new PDO('sqlite:' . $database_path);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Enable foreign keys
            $pdo->exec('PRAGMA foreign_keys = ON');
            
            // Create migrations table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS migrations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    migration VARCHAR NOT NULL,
                    batch INTEGER NOT NULL
                );
            ");

            // Create tasks table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS tasks (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title VARCHAR NOT NULL,
                    description TEXT NULL,
                    status VARCHAR NOT NULL DEFAULT 'pending',
                    due_date DATETIME NULL,
                    created_at DATETIME NOT NULL,
                    updated_at DATETIME NOT NULL
                );
            ");
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
        }
    }
}

// Set up Laravel environment paths
$_ENV['STORAGE_PATH'] = $storage_path;

// Create storage symlink if needed
$public_storage = $_SERVER['DOCUMENT_ROOT'] . '/storage';
if (!file_exists($public_storage)) {
    @symlink($storage_path . '/app/public', $public_storage);
}

// Load Composer's autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Get the kernel and handle the request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);