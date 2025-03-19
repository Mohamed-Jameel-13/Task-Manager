#!/bin/bash

# Install PHP
apt-get update
apt-get install -y php php-curl php-mbstring php-xml

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install Node.js dependencies and build assets
npm install
npm run build

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Generate Laravel caches
php artisan config:cache
php artisan route:cache
php artisan view:cache