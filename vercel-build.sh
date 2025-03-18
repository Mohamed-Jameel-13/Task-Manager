#!/bin/bash
set -e

echo "Running build script..."

# Skip Laravel cache commands as PHP is not available in the build environment
echo "PHP commands skipped in build script - will be handled by Vercel PHP runtime"

echo "Build completed"
