#!/bin/bash
set -e

echo "Running build script..."

# Install npm dependencies and build assets
npm ci
npm run build

# Create required directories first
mkdir -p /tmp/storage/framework/{sessions,views,cache}
mkdir -p /tmp/storage/logs
mkdir -p /tmp/bootstrap/cache

# Create database directory and file with proper permissions
mkdir -p /tmp
touch /tmp/database.sqlite
chmod 777 /tmp/database.sqlite
chmod -R 777 /tmp/storage
chmod -R 777 /tmp/bootstrap

# Initialize SQLite database and run migrations
echo "Running database migrations..."
php artisan migrate --force --no-interaction

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Generate application key if not set
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build process completed successfully"
