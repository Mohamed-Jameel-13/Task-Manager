<?php

// Set absolute paths for Vercel environment
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../public';
chdir($_SERVER['DOCUMENT_ROOT']);

// Check SQLite extension status for Vercel environment
if (getenv('VERCEL_ENV')) {
    // Output debug information only during deployment build time
    if (getenv('VERCEL_BUILD_STEP')) {
        echo "PDO SQLite Extension: " . (extension_loaded('pdo_sqlite') ? 'Loaded' : 'Not Loaded') . PHP_EOL;
        echo "SQLite3 Extension: " . (extension_loaded('sqlite3') ? 'Loaded' : 'Not Loaded') . PHP_EOL;
    }

    // Create SQLite database file if it doesn't exist
    $databasePath = '/tmp/database.sqlite';
    if (!file_exists($databasePath)) {
        if (getenv('VERCEL_BUILD_STEP')) {
            echo "Creating SQLite database at: $databasePath" . PHP_EOL;
        }
        
        touch($databasePath);
        chmod($databasePath, 0777);
        
        // Create a minimal database structure for migrations
        if (extension_loaded('pdo_sqlite')) {
            try {
                $pdo = new PDO('sqlite:' . $databasePath);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS migrations (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        migration VARCHAR NOT NULL,
                        batch INTEGER NOT NULL
                    );
                ");
                if (getenv('VERCEL_BUILD_STEP')) {
                    echo "Successfully created migrations table" . PHP_EOL;
                }
            } catch (Exception $e) {
                if (getenv('VERCEL_BUILD_STEP')) {
                    echo "Database initialization error: " . $e->getMessage() . PHP_EOL;
                }
            }
        } else if (getenv('VERCEL_BUILD_STEP')) {
            echo "Cannot create schema: pdo_sqlite extension not loaded" . PHP_EOL;
        }
    } else if (getenv('VERCEL_BUILD_STEP')) {
        echo "SQLite database already exists at: $databasePath" . PHP_EOL;
        echo "Database file is " . (is_writable($databasePath) ? 'writable' : 'not writable') . PHP_EOL;
    }
}

// Vercel SQLite database setup
$databasePath = '/tmp/database.sqlite';

// Only run this setup in production environment
if (!file_exists($databasePath) && (getenv('VERCEL_ENV') || getenv('VERCEL') || getenv('NOW_REGION'))) {
    touch($databasePath);
    chmod($databasePath, 0777);
    
    if (extension_loaded('pdo_sqlite')) {
        try {
            $pdo = new PDO('sqlite:' . $databasePath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS migrations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    migration VARCHAR NOT NULL,
                    batch INTEGER NOT NULL
                );
            ");
        } catch (Exception $e) {
            // Silent fail in production
        }
    }
}

// Forward Vercel requests to the public directory
require __DIR__ . '/../public/index.php';