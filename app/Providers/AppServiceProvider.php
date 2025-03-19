<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        // Enable SQLite foreign key support
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=1');
        }

        // Create required tmp directories for Vercel
        if (env('VERCEL_ENV')) {
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

            // Create SQLite database if it doesn't exist
            $databasePath = '/tmp/database.sqlite';
            if (!file_exists($databasePath)) {
                touch($databasePath);
                chmod($databasePath, 0777);
            }
        }

        Schema::defaultStringLength(191);
    }
}
