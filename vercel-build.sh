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

# Create an empty SQLite database with proper schema
echo "Creating empty SQLite database structure..."
cat > /tmp/init-db.sql << 'EOF'
CREATE TABLE IF NOT EXISTS "migrations" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "migration" VARCHAR NOT NULL,
    "batch" INTEGER NOT NULL
);

PRAGMA foreign_keys = ON;
EOF

# Initialize the database with the schema
if command -v sqlite3 &> /dev/null; then
    sqlite3 /tmp/database.sqlite ".read /tmp/init-db.sql"
    echo "SQLite database initialized with schema"

    # Run migrations if possible
    if [ -f "artisan" ]; then
        php artisan migrate --force
        echo "Migrations completed"
    fi
else
    echo "SQLite3 command not available during build, schema will be created at runtime"
fi

echo "Build process completed successfully"
