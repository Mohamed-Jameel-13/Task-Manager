<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use PDO;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (env('VERCEL_ENV')) {
            $this->app->useBootstrapPath('/tmp/bootstrap');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Create required tmp directories and database for Vercel first
        if (env('VERCEL_ENV')) {
            // Create database first
            $databasePath = '/tmp/database.sqlite';
            if (!file_exists($databasePath)) {
                touch($databasePath);
                chmod($databasePath, 0777);
            }

            try {
                $pdo = DB::connection()->getPdo();
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Check if tasks table exists
                $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tasks'");
                if (!$result->fetch()) {
                    // Run migrations if table is missing
                    \Artisan::call('migrate', ['--force' => true]);
                }
            } catch (\Exception $e) {
                \Log::error('Database initialization error: ' . $e->getMessage());
            }

            // Then create directories
            $dirs = [
                '/tmp/storage/app/public',
                '/tmp/storage/framework/cache',
                '/tmp/storage/framework/sessions',
                '/tmp/storage/framework/views',
                '/tmp/storage/logs',
                '/tmp/bootstrap/cache'
            ];

            foreach ($dirs as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                chmod($dir, 0777);
            }
        }

        // Enable SQLite foreign keys only after ensuring database exists
        if (DB::connection()->getDriverName() === 'sqlite') {
            try {
                DB::statement('PRAGMA foreign_keys=1');
            } catch (\Exception $e) {
                \Log::error('Failed to set SQLite foreign keys: ' . $e->getMessage());
            }
        }
    }
}
