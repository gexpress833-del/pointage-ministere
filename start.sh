#!/usr/bin/env bash
set -e

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Run seeders
echo "Running seeders..."
php artisan db:seed --class=ParametresSeeder --force --no-interaction

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

# Start the application
echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=80
