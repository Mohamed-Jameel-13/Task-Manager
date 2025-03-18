#!/bin/bash
<<<<<<< HEAD
# Install npm dependencies and build assets
npm ci
npm run build
=======
set -e

echo "Running build script..."

# Skip Laravel cache commands as PHP is not available in the build environment
echo "PHP commands skipped in build script - will be handled by Vercel PHP runtime"

echo "Build completed"
>>>>>>> 432e908168a490f3366d6d02ea49825f46638bc4
