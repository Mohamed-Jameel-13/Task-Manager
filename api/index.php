<?php
// Explicitly set content type
header("Content-Type: text/html; charset=utf-8");

// For Vercel deployment
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set document root and working directory
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../public';
chdir($_SERVER['DOCUMENT_ROOT']);

// Setup environment paths for Vercel
$database_path = '/tmp/database.sqlite';
$storage_path = '/tmp/storage';
$bootstrap_path = '/tmp/bootstrap';

// Create necessary directories
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

if (!file_exists($bootstrap_path)) {
    mkdir($bootstrap_path, 0777, true);
    mkdir($bootstrap_path . '/cache', 0777, true);
}

// Set up database
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

            // Manual migrations - create tasks table if it doesn't exist
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
            // Log errors
            error_log("Database error: " . $e->getMessage());
        }
    }
}

// Important: Configure Laravel to use these paths
putenv("DB_DATABASE={$database_path}");
putenv("DB_CONNECTION=sqlite");
putenv("CACHE_DRIVER=file");
putenv("SESSION_DRIVER=cookie");
putenv("VIEW_COMPILED_PATH={$bootstrap_path}/cache");
putenv("STORAGE_PATH={$storage_path}");

$_ENV['DB_DATABASE'] = $database_path;
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['CACHE_DRIVER'] = 'file';
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['VIEW_COMPILED_PATH'] = $bootstrap_path . '/cache';
$_ENV['STORAGE_PATH'] = $storage_path;
$_ENV['APP_KEY'] = 'base64:JT+DhYrz/heCTsLh5M5+5yMRO9b2zOgE+89p7Lrne9g=';

// Set storage directory symlink
$public_storage = $_SERVER['DOCUMENT_ROOT'] . '/storage';
if (!file_exists($public_storage)) {
    @symlink($storage_path, $public_storage);
}

// Boot the Laravel application directly
require __DIR__ . '/../public/index.php';