#!/bin/bash
echo "Starting deployment process..."

# Navigate to the base directory of the project
cd "$(dirname "$0")"

# Pull the latest changes from the main branch
git pull origin main

# Install dependencies based on the lock file without interaction
composer install --no-interaction --prefer-dist --optimize-autoloader

# Clear all caches (config, routes, views, etc.)
php artisan optimize:clear

# Run database migrations
php artisan migrate --force

echo "Deployment finished successfully."
