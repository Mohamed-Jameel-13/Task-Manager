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

echo "Build process completed successfully"
