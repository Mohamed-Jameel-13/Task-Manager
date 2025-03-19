#!/bin/bash
set -e
echo "Running build script..."

# Create .env file directly instead of copying
echo "Creating .env file..."
cat > .env << 'EOF'
APP_NAME="Task Manager"
APP_ENV=production
APP_KEY=base64:JT+DhYrz/heCTsLh5M5+5yMRO9b2zOgE+89p7Lrne9g=
APP_DEBUG=false
APP_URL=https://task-manager-vercel.vercel.app

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=/tmp/database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
EOF

# Run npm build instead of composer install
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

CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR NOT NULL,
    description TEXT NULL,
    status VARCHAR NOT NULL DEFAULT 'pending',
    due_date DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);
EOF

if command -v sqlite3 &> /dev/null; then
    sqlite3 /tmp/database.sqlite ".read /tmp/init-db.sql"
    echo "Database initialized successfully"
else
    echo "SQLite3 command not available, will initialize database at runtime"
fi

echo "Build completed successfully!"
