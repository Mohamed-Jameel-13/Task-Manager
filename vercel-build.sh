#!/bin/bash
set -e

echo "Running build script..."

# Download Composer
curl -sS https://getcomposer.org/installer -o composer-setup.php

# Run the installer
php composer-setup.php

# Move Composer to a globally accessible location
mv composer.phar /usr/local/bin/composer

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Create required directories
mkdir -p /tmp/storage/framework/{sessions,views,cache}
mkdir -p /tmp/storage/logs
mkdir -p /tmp/bootstrap/cache

# Create database directory and file with proper permissions
mkdir -p /tmp
touch /tmp/database.sqlite
chmod 777 /tmp/database.sqlite
chmod -R 777 /tmp/storage
chmod -R 777 /tmp/bootstrap

# Initialize SQLite database
echo "Initializing SQLite database..."
cat > /tmp/init-db.sql << 'EOF'
CREATE TABLE IF NOT EXISTS migrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    migration VARCHAR NOT NULL,
    batch INTEGER NOT NULL
);
EOF

if command -v sqlite3 &> /dev/null; then
    sqlite3 /tmp/database.sqlite ".read /tmp/init-db.sql"
    echo "Database initialized successfully"
    
    # Run Laravel migrations
    echo "Running database migrations..."
    php artisan migrate:fresh --force --no-interaction
else
    echo "SQLite3 command not available, will initialize database at runtime"
fi

# Cache configuration for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build completed successfully"
