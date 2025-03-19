<?php

// This file is used exclusively by Vercel deployment
// It should NOT be included by api/index.php

// Enable error reporting for deployment debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// This file should not be accessed directly in the Vercel environment
// It should only be required by the main Laravel bootstrap process
if (isset($_SERVER['REQUEST_URI']) && !getenv('VERCEL_LOCAL_DEV')) {
    http_response_code(404);
    exit('Not found');
}

// Set environment variables for Laravel
$database_path = '/tmp/database.sqlite';
$storage_path = '/tmp/storage';
$bootstrap_path = '/tmp/bootstrap';

// Important: Configure Laravel to use these paths
putenv("DB_DATABASE={$database_path}");
putenv("DB_CONNECTION=sqlite");
putenv("CACHE_DRIVER=file");
putenv("SESSION_DRIVER=cookie");
putenv("VIEW_COMPILED_PATH={$bootstrap_path}/cache");
putenv("STORAGE_PATH={$storage_path}");

// Set as environment variables too
$_ENV['DB_DATABASE'] = $database_path;
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['CACHE_DRIVER'] = 'file';
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['VIEW_COMPILED_PATH'] = $bootstrap_path . '/cache';
$_ENV['STORAGE_PATH'] = $storage_path;
$_ENV['APP_KEY'] = 'base64:JT+DhYrz/heCTsLh5M5+5yMRO9b2zOgE+89p7Lrne9g=';

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
            
            // Check if migrations table exists
            $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='migrations'");
            if (!$result->fetch()) {
                // Create migrations table
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS migrations (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        migration VARCHAR NOT NULL,
                        batch INTEGER NOT NULL
                    )
                ");
                
                // Run migrations
                chdir(dirname(__DIR__));
                passthru('php artisan migrate --force --no-interaction 2>&1', $return_var);
                if ($return_var !== 0) {
                    error_log("Migration failed with status: " . $return_var);
                }
            } else {
                // Check if tasks table exists
                $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tasks'");
                if (!$result->fetch()) {
                    // Run migrations if tasks table is missing
                    chdir(dirname(__DIR__));
                    passthru('php artisan migrate --force --no-interaction 2>&1', $return_var);
                    if ($return_var !== 0) {
                        error_log("Migration failed with status: " . $return_var);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Database initialization error: " . $e->getMessage());
        }
    }
}

// Set storage directory symlink
$public_storage = __DIR__ . '/storage';
if (!file_exists($public_storage)) {
    @symlink($storage_path, $public_storage);
}

// Continue with normal application bootstrapping
require __DIR__ . '/index.php';