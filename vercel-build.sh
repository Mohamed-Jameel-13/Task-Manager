#!/bin/bash

# Install frontend dependencies and build assets
npm install
npm run build

# Set up directories
mkdir -p bootstrap/cache
mkdir -p storage/framework/{sessions,views,cache}

# Create storage symlink
ln -s ../storage/app/public public/storage