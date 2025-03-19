#!/bin/bash
set -e

echo "Running build script..."

# Install npm dependencies and build assets
npm ci
npm run build

# Create database directory
mkdir -p /tmp
touch /tmp/database.sqlite

echo "Created SQLite database at /tmp/database.sqlite"
echo "Build completed"
