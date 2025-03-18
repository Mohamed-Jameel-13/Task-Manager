#!/bin/bash
# Install npm dependencies and build assets
npm ci
npm run build

# Create SQLite database in the writable /tmp directory
mkdir -p /tmp
touch /tmp/database.sqlite
php -r "file_exists('.env.production') && copy('.env.production', '.env');"

# If we have a database migration file, try to migrate
if [ -f "database/migrations/2023_05_10_000000_create_tasks_table.php" ]; then
  php artisan migrate --force --no-interaction
fi