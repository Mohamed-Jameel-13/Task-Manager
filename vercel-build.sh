#!/bin/bash
set -e

# Ensure the script doesn't fail if PHP is not in path
echo "Running build script..."

# Vite build is already being run by Vercel
# npm run build

# Generate Laravel caches if PHP is available
if command -v php &> /dev/null; then
    echo "Generating Laravel caches..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    echo "PHP not available, skipping Laravel cache generation"
fi

echo "Build completed"
