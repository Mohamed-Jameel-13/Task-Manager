#!/bin/bash

# Create directories needed by Laravel
mkdir -p bootstrap/cache
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs

# Create storage symlink if it doesn't exist
if [ ! -L public/storage ]; then
    ln -s ../storage/app/public public/storage
fi

# Set proper permissions
chmod -R 777 storage bootstrap/cache

# Generate Laravel caches if we're in production
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi